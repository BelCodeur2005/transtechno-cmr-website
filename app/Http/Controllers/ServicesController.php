<?php

namespace App\Http\Controllers;

use App\Filament\Resources\PoleProjectRequestResource;
use App\Mail\LeadAcknowledgement;
use App\Mail\LeadNotification;
use App\Models\PoleProjectRequest;
use App\Models\SavTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ServicesController extends Controller
{
    /** Natures de projet proposées sur la landing Hémodialyse. */
    private const PROJECT_TYPES = [
        'Création complète d\'un centre',
        'Achat de générateurs',
        'Contrat de maintenance',
        'Fourniture de dialysats / consommables',
    ];

    /** Natures de demande proposées sur la landing partenaire ABM (réactifs). */
    private const ABM_REQUEST_TYPES = [
        'Biologie moléculaire (PCR / qPCR / RT-PCR)',
        'Extraction & purification ADN / ARN',
        'Microbiologie & milieux de culture',
        'Culture cellulaire',
        'Génomique & séquençage',
        'CRISPR & édition génique',
        'Approvisionnement récurrent / contrat',
        'Autre demande technique',
    ];

    /** Types d'intervention proposés sur le formulaire SAV public. */
    private const INTERVENTION_TYPES = [
        'Réparation / Panne',
        'Installation & mise en service',
        'Maintenance préventive',
        'Formation des utilisateurs',
        'Fourniture de pièces de rechange',
        'Contrôle & étalonnage',
        'Autre',
    ];

    /**
     * Page Services & SAV (4 piliers + processus + hotline).
     */
    public function sav(): Response
    {
        return Inertia::render('Public/ServicesSav');
    }

    /**
     * Formulaire dédié d'ouverture de ticket SAV (page complète).
     */
    public function savDemande(): Response
    {
        return Inertia::render('Public/SavDemande', [
            'interventionTypes' => self::INTERVENTION_TYPES,
        ]);
    }

    /**
     * Landing Pôle Hémodialyse & Traitement des eaux.
     */
    public function hemodialyse(): Response
    {
        return Inertia::render('Public/PoleHemodialyse', [
            'projectTypes' => self::PROJECT_TYPES,
        ]);
    }

    /**
     * Landing partenaire ABM (Applied Biological Materials) — réactifs & biologie moléculaire.
     */
    public function abm(): Response
    {
        // Les domaines présentés viennent des catégories réactifs actives (administrables, désactivables).
        $domains = \App\Models\Category::query()
            ->where('type', 'reactif')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get(['name', 'slug', 'icon', 'description'])
            ->map(fn ($c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon ?: 'science',
                'description' => $c->description,
            ]);

        return Inertia::render('Public/Reactifs', [
            'requestTypes' => self::ABM_REQUEST_TYPES,
            'domains' => $domains,
        ]);
    }

    /**
     * Enregistre une demande spécialisée réactifs (formulaire de la landing ABM).
     */
    public function storeAbm(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'structure' => ['required', 'string', 'max:255'],
            'function' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'request_type' => ['required', 'string', 'in:' . implode(',', self::ABM_REQUEST_TYPES)],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $message = "Nature de la demande : {$data['request_type']}\n";
        if (! empty($data['function'])) {
            $message .= "Fonction du contact : {$data['function']}\n";
        }
        if (! empty($data['message'])) {
            $message .= "\n" . $data['message'];
        }

        $project = PoleProjectRequest::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'structure' => $data['structure'],
            'pole_type' => 'abm',
            'message' => $message,
            'status' => 'nouveau',
        ]);

        $this->sendAbmNotifications($project, $data['request_type']);

        return back()->with('success', 'Votre demande a bien été transmise à notre spécialiste réactifs & biologie moléculaire.');
    }

    /**
     * Enregistre une demande de ticket SAV soumise depuis la page publique.
     * Crée un SavTicket sans client_id ni equipment_id — l'admin les lie ensuite.
     */
    public function storeSavDemande(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'structure'               => ['required', 'string', 'max:255'],
            'city'                    => ['nullable', 'string', 'max:255'],
            'phone'                   => ['required', 'string', 'max:50'],
            'email'                   => ['required', 'email', 'max:255'],
            'intervention_type'       => ['required', 'string', 'in:' . implode(',', self::INTERVENTION_TYPES)],
            'equipments'              => ['nullable', 'array', 'max:10'],
            'equipments.*.brand'      => ['nullable', 'string', 'max:255'],
            'equipments.*.model'      => ['nullable', 'string', 'max:255'],
            'equipments.*.serial'     => ['nullable', 'string', 'max:255'],
            'problem'                 => ['required', 'string', 'max:5000'],
            'priority'                => ['required', 'string', 'in:Normal,Critique,Urgent'],
        ], [
            'name.required'              => 'Le nom complet est obligatoire.',
            'structure.required'         => 'L\'établissement est obligatoire.',
            'phone.required'             => 'Le numéro de téléphone est obligatoire.',
            'email.email'                => 'L\'adresse email n\'est pas valide.',
            'intervention_type.required' => 'Veuillez sélectionner un type d\'intervention.',
            'intervention_type.in'       => 'Le type d\'intervention sélectionné est invalide.',
            'problem.required'           => 'La description de la panne ou du besoin est obligatoire.',
            'priority.required'          => 'Veuillez sélectionner un niveau de priorité.',
        ]);

        $lines = ["[Demande en ligne]"];
        $lines[] = "Contact : {$data['name']} — {$data['structure']}" . (! empty($data['city']) ? ", {$data['city']}" : '');
        $lines[] = "Tél : {$data['phone']}" . (! empty($data['email']) ? " | Email : {$data['email']}" : '');
        $lines[] = "Type d'intervention : {$data['intervention_type']}";

        $equipmentSummaries = [];
        foreach ($data['equipments'] ?? [] as $i => $eq) {
            $brand  = trim($eq['brand'] ?? '');
            $model  = trim($eq['model'] ?? '');
            $serial = trim($eq['serial'] ?? '');
            if ($brand || $model) {
                $label = trim("$brand $model");
                if ($serial) {
                    $label .= " — N° série : $serial";
                }
                $equipmentSummaries[] = $label;
                $prefix = count($data['equipments']) > 1 ? 'Équipement #' . ($i + 1) : 'Équipement';
                $lines[] = "$prefix : $label";
            }
        }

        $lines[] = '';
        $lines[] = "DESCRIPTION :";
        $lines[] = $data['problem'];

        $ticket = SavTicket::create([
            'description' => implode("\n", $lines),
            'status'      => 'Ouvert',
            'priority'    => $data['priority'],
            'opened_at'   => now(),
        ]);

        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotification(
                subjectLine: 'Nouveau ticket SAV #' . $ticket->reference . ' — ' . $data['structure'],
                heading: 'Ticket SAV ' . $ticket->reference,
                intro: "Un ticket SAV vient d'être ouvert depuis le site public.",
                rows: [
                    'Référence'         => '#' . $ticket->reference,
                    'Contact'           => $data['name'],
                    'Établissement'     => $data['structure'],
                    'Ville'             => $data['city'] ?? '—',
                    'Téléphone'         => $data['phone'],
                    'Email'             => $data['email'] ?? '—',
                    'Type intervention' => $data['intervention_type'],
                    'Équipement(s)'     => $equipmentSummaries ? implode(' / ', $equipmentSummaries) : '—',
                    'Priorité'          => $ticket->priority,
                ],
                messageBody: $data['problem'],
                actionUrl: url('/admin/sav-tickets/' . $ticket->getKey() . '/edit'),
                actionLabel: 'Traiter le ticket',
            ));

            if (! empty($data['email'])) {
                Mail::to($data['email'])->send(new LeadAcknowledgement(
                    subjectLine: 'Votre ticket SAV ' . $ticket->reference . ' — Transtechno Cameroun',
                    recipientName: $data['name'],
                    intro: "Votre demande d'assistance technique a bien été reçue. Notre équipe SAV vous recontactera dans les meilleurs délais pour planifier l'intervention.",
                    reference: $ticket->reference,
                    details: ['Établissement' => $data['structure'], 'Priorité' => $ticket->priority],
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email ticket SAV #' . $ticket->reference . ' : ' . $e->getMessage());
        }

        return back()->with('success', 'Votre ticket SAV ' . $ticket->reference . ' a bien été ouvert. Notre équipe technique vous recontacte sous 24 h.');
    }

    /**
     * Enregistre une demande de projet (formulaire de la landing Hémodialyse).
     */
    public function storeProject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'structure' => ['required', 'string', 'max:255'],
            'function' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'project_type' => ['required', 'string', 'in:' . implode(',', self::PROJECT_TYPES)],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        // Fonction + nature du projet consignées dans le message (pas de colonnes dédiées).
        $message = "Nature du projet : {$data['project_type']}\n";
        if (! empty($data['function'])) {
            $message .= "Fonction du contact : {$data['function']}\n";
        }
        if (! empty($data['message'])) {
            $message .= "\n" . $data['message'];
        }

        $project = PoleProjectRequest::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'structure' => $data['structure'],
            'pole_type' => 'hemodialyse',
            'message' => $message,
            'status' => 'nouveau',
        ]);

        $this->sendNotifications($project, $data['project_type']);

        return back()->with('success', 'Votre demande de projet a bien été transmise à notre département d\'ingénierie biomédicale.');
    }

    /** Notification interne + accusé de réception (sans bloquer la soumission). */
    private function sendNotifications(PoleProjectRequest $project, string $projectType): void
    {
        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotification(
                subjectLine: 'Nouveau projet Hémodialyse — ' . $project->structure,
                heading: 'Demande de projet (Pôle Hémodialyse)',
                intro: "Une demande de projet vient d'être soumise depuis la landing Hémodialyse.",
                rows: [
                    'Contact' => $project->name,
                    'Établissement' => $project->structure,
                    'Téléphone' => $project->phone,
                    'Email' => $project->email,
                    'Nature du projet' => $projectType,
                ],
                messageBody: $project->message,
                actionUrl: PoleProjectRequestResource::getUrl('edit', ['record' => $project->getKey()]),
                actionLabel: 'Voir la demande',
            ));

            if ($project->email) {
                Mail::to($project->email)->send(new LeadAcknowledgement(
                    subjectLine: 'Votre projet de centre de dialyse — Transtechno Cameroun',
                    recipientName: $project->name,
                    intro: "Merci pour votre demande. Notre département d'ingénierie biomédicale étudie votre projet et vous recontactera pour le dimensionnement technique et financier.",
                    details: ['Nature du projet' => $projectType, 'Établissement' => $project->structure],
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email projet : ' . $e->getMessage());
        }
    }

    /** Notification interne + accusé de réception pour une demande réactifs ABM. */
    private function sendAbmNotifications(PoleProjectRequest $project, string $requestType): void
    {
        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotification(
                subjectLine: 'Nouvelle demande Réactifs ABM — ' . $project->structure,
                heading: 'Demande spécialisée (Réactifs & Biologie moléculaire)',
                intro: "Une demande vient d'être soumise depuis la landing partenaire ABM.",
                rows: [
                    'Contact' => $project->name,
                    'Laboratoire / Structure' => $project->structure,
                    'Téléphone' => $project->phone,
                    'Email' => $project->email,
                    'Nature de la demande' => $requestType,
                ],
                messageBody: $project->message,
                actionUrl: PoleProjectRequestResource::getUrl('edit', ['record' => $project->getKey()]),
                actionLabel: 'Voir la demande',
            ));

            if ($project->email) {
                Mail::to($project->email)->send(new LeadAcknowledgement(
                    subjectLine: 'Votre demande de réactifs ABM — Transtechno Cameroun',
                    recipientName: $project->name,
                    intro: "Merci pour votre demande. Notre spécialiste réactifs vous recontactera pour la disponibilité, les conditions de conservation (chaîne du froid) et la tarification de vos consommables.",
                    details: ['Nature de la demande' => $requestType, 'Laboratoire' => $project->structure],
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email demande ABM : ' . $e->getMessage());
        }
    }
}
