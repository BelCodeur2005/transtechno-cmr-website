<?php

namespace App\Http\Controllers;

use App\Filament\Resources\QuotationResource;
use App\Mail\LeadAcknowledgement;
use App\Mail\LeadNotification;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationStatusHistory;
use App\Support\DevisPdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class QuotationController extends Controller
{
    /** Secteurs d'activité autorisés (cf. enum migration quotations). */
    private const SECTORS = ['Privé', 'Public', 'ONG', 'Recherche'];

    /**
     * Récapitulatif : le visiteur finalise les produits choisis (panier localStorage).
     * Le panier est rendu côté client ; cette page n'a pas besoin de props serveur.
     */
    public function recap(): Response
    {
        return Inertia::render('Public/Recapitulatif', [
            'sectors' => self::SECTORS,
        ]);
    }

    /**
     * Demande / importation : le visiteur n'a pas trouvé son produit et décrit son besoin.
     */
    public function demande(): Response
    {
        return Inertia::render('Public/DemandeCotation', [
            'sectors' => self::SECTORS,
        ]);
    }

    /**
     * Enregistre une cotation issue du panier (récapitulatif).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'responsible_name' => ['required', 'string', 'max:255'],
            'structure_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'in:' . implode(',', self::SECTORS)],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'specifications' => ['nullable', 'string', 'max:5000'],
            'is_urgent' => ['boolean'],
            'services_installation' => ['boolean'],
            'services_formation' => ['boolean'],
            'services_maintenance' => ['boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.custom_label' => ['nullable', 'string', 'max:255'],
            'items.*.brand_name' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        // Chaque ligne doit référencer soit un produit du catalogue, soit un libellé hors-catalogue.
        foreach ($data['items'] as $i => $item) {
            if (empty($item['product_id']) && empty($item['custom_label'])) {
                return back()
                    ->withErrors(["items.$i" => 'Chaque article doit référencer un produit ou un libellé hors-catalogue.'])
                    ->withInput();
            }
        }

        $quotation = $this->persist($data, $data['items']);
        $this->sendNotifications($quotation);

        return redirect()
            ->route('cotation.confirmation', $quotation->reference)
            ->with('success', 'Votre demande de cotation a bien été enregistrée.');
    }

    /**
     * Enregistre une demande d'importation (formulaire libre, sans panier).
     */
    public function storeDemande(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'responsible_name' => ['required', 'string', 'max:255'],
            'structure_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'in:' . implode(',', self::SECTORS)],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'specifications' => ['required', 'string', 'max:5000'],
            'is_urgent' => ['boolean'],
            'services_installation' => ['boolean'],
            'services_formation' => ['boolean'],
            'services_maintenance' => ['boolean'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'responsible_name.required' => 'Le nom du responsable est obligatoire.',
            'structure_name.required' => 'Le nom de la structure est obligatoire.',
            'city.required' => 'La ville est obligatoire.',
            'sector.required' => 'Le secteur d\'activité est obligatoire.',
            'specifications.required' => 'La description des équipements souhaités est obligatoire.',
        ]);

        $quotation = $this->persist($data, []);
        $this->sendNotifications($quotation);

        return redirect()
            ->route('cotation.confirmation', $quotation->reference)
            ->with('success', 'Votre demande a bien été transmise à notre équipe.');
    }

    /**
     * Page de confirmation après envoi (le panier est vidé côté client).
     */
    public function confirmation(string $reference): Response
    {
        $quotation = Quotation::where('reference', $reference)->firstOrFail();

        return Inertia::render('Public/CotationConfirmation', [
            'reference' => $quotation->reference,
            'isUrgent' => $quotation->is_urgent,
        ]);
    }

    /**
     * Génère le devis PDF (DomPDF). Affiché inline dans le navigateur ;
     * ?dl=1 force le téléchargement.
     */
    public function devis(Quotation $quotation, Request $request): HttpResponse
    {
        $pdf = DevisPdf::render($quotation);
        $filename = DevisPdf::filename($quotation);

        return $request->boolean('dl')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }

    /**
     * Notifie l'équipe interne et envoie un accusé de réception au visiteur.
     * Les échecs d'envoi n'interrompent jamais la soumission du formulaire.
     */
    private function sendNotifications(Quotation $quotation): void
    {
        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotification(
                subjectLine: 'Nouvelle cotation #' . $quotation->reference . ' — ' . $quotation->structure_name,
                heading: 'Demande de cotation ' . $quotation->reference,
                intro: "Une nouvelle demande de cotation vient d'être soumise depuis le site.",
                rows: [
                    'Référence' => '#' . $quotation->reference,
                    'Structure' => $quotation->structure_name,
                    'Responsable' => $quotation->responsible_name,
                    'Ville' => $quotation->city,
                    'Secteur' => $quotation->sector,
                    'Téléphone' => $quotation->phone,
                    'Email' => $quotation->email,
                    'Articles' => $quotation->items()->count() . ' ligne(s)',
                    'Priorité' => $quotation->priority,
                ],
                messageBody: $quotation->specifications,
                actionUrl: QuotationResource::getUrl('edit', ['record' => $quotation->getKey()]),
                actionLabel: 'Traiter la cotation',
            ));

            if ($quotation->email) {
                Mail::to($quotation->email)->send(new LeadAcknowledgement(
                    subjectLine: 'Votre demande de cotation ' . $quotation->reference . ' — Transtechno Cameroun',
                    recipientName: $quotation->responsible_name,
                    intro: 'Nous avons bien reçu votre demande de cotation. Elle est en cours de traitement par notre équipe commerciale.',
                    reference: $quotation->reference,
                    details: ['Structure' => $quotation->structure_name, 'Ville' => $quotation->city],
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email cotation #' . $quotation->reference . ' : ' . $e->getMessage());
        }
    }

    /**
     * Persiste la cotation + ses lignes dans une transaction.
     *
     * @param  array<string,mixed>  $data
     * @param  array<int,array<string,mixed>>  $items
     */
    private function persist(array $data, array $items): Quotation
    {
        return DB::transaction(function () use ($data, $items) {
            $quotation = Quotation::create([
                'reference' => $this->generateReference(),
                'status' => 'Nouveau',
                'priority' => ($data['is_urgent'] ?? false) ? 'Urgent' : 'Normal',
                'responsible_name' => $data['responsible_name'],
                'structure_name' => $data['structure_name'],
                'city' => $data['city'],
                'sector' => $data['sector'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'specifications' => $data['specifications'] ?? null,
                'is_urgent' => $data['is_urgent'] ?? false,
                'services_installation' => $data['services_installation'] ?? false,
                'services_formation' => $data['services_formation'] ?? false,
                'services_maintenance' => $data['services_maintenance'] ?? false,
            ]);

            foreach ($items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'] ?? null,
                    'custom_label' => $item['custom_label'] ?? null,
                    'brand_name' => $item['brand_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price_fcfa' => 0, // tarifé par l'admin lors du traitement
                ]);
            }

            QuotationStatusHistory::create([
                'quotation_id' => $quotation->id,
                'old_status' => null,
                'new_status' => 'Nouveau',
                'note' => 'Demande créée depuis le site public.',
            ]);

            return $quotation;
        });
    }

    /**
     * Génère une référence unique au format TR-{année}-{séquence sur 3 chiffres}.
     * La séquence repart à 1 chaque année.
     */
    private function generateReference(): string
    {
        $year = now()->year;
        $count = Quotation::where('reference', 'like', "TR-{$year}-%")->count();

        do {
            $count++;
            $reference = sprintf('TR-%d-%03d', $year, $count);
        } while (Quotation::where('reference', $reference)->exists());

        return $reference;
    }
}
