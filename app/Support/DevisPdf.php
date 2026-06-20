<?php

namespace App\Support;

use App\Models\Quotation;
use App\Models\Setting;
use App\Support\NombrEnLettres;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

/**
 * Génère le devis PDF d'une cotation. Source unique partagée par la route
 * publique (aperçu/téléchargement) et l'envoi par email.
 */
class DevisPdf
{
    public static function render(Quotation $quotation): DomPdf
    {
        $quotation->loadMissing(['items.product.brand', 'assignedUser']);

        $subtotal        = $quotation->subtotalHt();
        $discountPercent = (int) $quotation->discount_percent;
        $discountAmount  = (int) round($subtotal * $discountPercent / 100);
        $afterDiscount   = $subtotal - $discountAmount;

        $installationCost = $quotation->services_installation ? (int) $quotation->service_installation_cost : 0;
        $formationCost    = $quotation->services_formation    ? (int) $quotation->service_formation_cost    : 0;
        $maintenanceCost  = $quotation->services_maintenance  ? (int) $quotation->service_maintenance_cost  : 0;
        $servicesTotal    = $installationCost + $formationCost + $maintenanceCost;

        $transport = (int) $quotation->transport_cost;
        $base      = $afterDiscount + $servicesTotal + $transport;
        $tva       = $quotation->vat_exempt ? 0 : (int) round($base * 0.1925);

        $issuedAt = $quotation->created_at ?? now();

        $months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        $dateFr = $issuedAt->day . ' ' . $months[$issuedAt->month - 1] . ' ' . $issuedAt->year;
        $totalAmount = $base + $tva;

        return Pdf::loadView('pdf.devis', [
            'company_email'    => Setting::get('email',        'office@transtechno-cmr.com'),
            'company_phone_1'  => Setting::get('phone_1',      '+237 674 64 53 54'),
            'company_phone_2'  => Setting::get('phone_2',      '+237 699 67 40 36'),
            'company_phone_3'  => Setting::get('phone_3',      '+237 233 37 33 62'),
            'company_address1' => Setting::get('address_line1','Village carrefour, entrée bille'),
            'company_address2' => Setting::get('address_line2','Immeuble Pharmacie Sainte-Marie, 2ème niveau'),
            'company_city'     => Setting::get('address_city', 'Douala, Cameroun'),
            'company_rccm'     => Setting::get('rccm',         'RC/BFM/B/586'),
            'company_niu'      => Setting::get('niu',          'M111914366866R'),
            'company_rib'      => Setting::get('rib',          '10033-05203-03016000260-31'),
            'company_cnps'     => Setting::get('cnps',         '380-0113880-000-E'),
            'quotation'        => $quotation,
            'logo'             => file_exists(public_path('images/logo-facture.jpeg'))
                                    ? public_path('images/logo-facture.jpeg')
                                    : (file_exists(public_path('images/logo.png')) ? public_path('images/logo.png') : null),
            'dateFr'           => $dateFr,
            'validUntilFr'     => $issuedAt->copy()->addDays(30)->day . ' ' . $months[$issuedAt->copy()->addDays(30)->month - 1] . ' ' . $issuedAt->copy()->addDays(30)->year,
            'subtotal'         => $subtotal,
            'discountPercent'  => $discountPercent,
            'discountAmount'   => $discountAmount,
            'installationCost' => $installationCost,
            'formationCost'    => $formationCost,
            'maintenanceCost'  => $maintenanceCost,
            'servicesTotal'    => $servicesTotal,
            'transport'        => $transport,
            'tva'              => $tva,
            'total'            => $totalAmount,
            'totalEnLettres'   => NombrEnLettres::convert($totalAmount),
            'fmt'              => fn (int $n) => number_format($n, 0, ',', '.'),
        ])->setPaper('a4');
    }

    public static function filename(Quotation $quotation): string
    {
        return 'Devis-' . $quotation->reference . '.pdf';
    }
}
