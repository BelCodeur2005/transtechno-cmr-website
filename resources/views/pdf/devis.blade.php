<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Devis {{ $quotation->reference }}</title>
    <style>
        @page { size: A4; margin: 20mm 18mm 28mm 18mm; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* ── Tableau articles ── */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-weight: bold;
            text-align: center;
            background-color: #ffffff;
            color: #000000;
        }
        table.items td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
            color: #000000;
        }
        .cell-design { text-align: left; }
        .cell-qty    { text-align: center; }
        .cell-price  { text-align: right; white-space: nowrap; }
        tr.row-total td { font-weight: bold; }

        /* ── Arrêté + Signature ── */
        .arrete    { margin-top: 16px; }
        .signature { margin-top: 38px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

{{-- ════════════════════════════════════════════════════
     Tableau global — thead répété sur chaque page par DomPDF,
     tbody contient tout le contenu principal.
     Le footer (position:fixed) est en dehors du tableau.
════════════════════════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse;">

    {{-- ════════ EN-TÊTE (répété sur chaque page) ════════ --}}
    <thead>
        <tr>
            <td style="padding-bottom:14px;">
                <table style="width:100%; border-collapse:collapse;">
                    <colgroup>
                        <col style="width:44%;">
                        <col style="width:56%;">
                    </colgroup>
                    <tr>
                        <td style="vertical-align:middle;">
                            @if ($logo)
                                <img src="{{ $logo }}"
                                     style="max-height:24mm; max-width:76mm; width:auto; height:auto; display:block;"
                                     alt="Transtechno Cameroun">
                            @else
                                <strong style="font-size:18px;">TRANSTECHNO</strong>
                            @endif
                        </td>
                        <td style="vertical-align:top; text-align:right; font-size:10.5px; line-height:1.85; color:#000000;">
                            <strong>Email :</strong>
                            <span style="text-decoration:underline;">{{ $company_email }}</span><br>
                            Agréée MINSANTE : Arrêté N°0683<br>
                            Tél : {{ $company_phone_1 }} / {{ $company_phone_2 }}<br>
                            Fixe : {{ $company_phone_3 }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </thead>

    {{-- ════════ CONTENU PRINCIPAL ════════ --}}
    <tbody>
        <tr>
            <td style="vertical-align:top;">

                {{-- DATE --}}
                <div style="text-align:center; margin-bottom:14px; color:#000000; padding-left:30%;">Douala, le {{ $dateFr }}</div>

                {{-- DESTINATAIRE --}}
                <div style="text-align:center; margin-bottom:20px; line-height:1.8; color:#000000; padding-left:30%;">
                    A<br>
                    <strong style="font-size:12px;">{{ strtoupper($quotation->structure_name) }}</strong>
                </div>

                {{-- RÉFÉRENCE --}}
                <div style="font-weight:bold; text-decoration:underline; margin-bottom:10px; color:#000000;">
                    DEVIS N° : {{ $quotation->reference }}
                </div>

                {{-- TABLEAU ARTICLES --}}
                <table class="items">
                    <thead>
                        <tr>
                            <th style="width:55%;">DESIGNATION</th>
                            <th style="width:10%;">QTE</th>
                            <th style="width:17.5%;">PRIX UNITAIRE</th>
                            <th style="width:17.5%;">PRIX TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($quotation->items as $item)
                            <tr>
                                <td class="cell-design">{{ strtoupper($item->product?->name ?? $item->custom_label ?? '—') }}</td>
                                <td class="cell-qty">{{ str_pad($item->quantity, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="cell-price">{{ $fmt($item->unit_price_fcfa) }}</td>
                                <td class="cell-price">{{ $fmt($item->quantity * $item->unit_price_fcfa) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:14px;">—</td>
                            </tr>
                        @endforelse

                        {{-- Services : DESIGNATION + QTE fusionnés --}}
                        @if ($installationCost > 0)
                        <tr>
                            <td colspan="2" class="cell-design">INSTALLATION &amp; MISE EN SERVICE</td>
                            <td class="cell-price">{{ $fmt($installationCost) }}</td>
                            <td class="cell-price">{{ $fmt($installationCost) }}</td>
                        </tr>
                        @endif
                        @if ($formationCost > 0)
                        <tr>
                            <td colspan="2" class="cell-design">FORMATION TECHNIQUE DU PERSONNEL</td>
                            <td class="cell-price">{{ $fmt($formationCost) }}</td>
                            <td class="cell-price">{{ $fmt($formationCost) }}</td>
                        </tr>
                        @endif
                        @if ($maintenanceCost > 0)
                        <tr>
                            <td colspan="2" class="cell-design">CONTRAT DE MAINTENANCE PRÉVENTIVE</td>
                            <td class="cell-price">{{ $fmt($maintenanceCost) }}</td>
                            <td class="cell-price">{{ $fmt($maintenanceCost) }}</td>
                        </tr>
                        @endif
                        @if ($transport > 0)
                        <tr>
                            <td colspan="2" class="cell-design">FRAIS DE TRANSPORT / LOGISTIQUE</td>
                            <td class="cell-price">{{ $fmt($transport) }}</td>
                            <td class="cell-price">{{ $fmt($transport) }}</td>
                        </tr>
                        @endif

                        @if ($discountPercent > 0)
                        <tr>
                            <td colspan="3" style="text-align:right; padding:5px 8px; color:#000000;">Remise {{ $discountPercent }} %</td>
                            <td class="cell-price">- {{ $fmt($discountAmount) }}</td>
                        </tr>
                        @endif

                        @if (! $quotation->vat_exempt)
                        <tr>
                            <td colspan="3" style="text-align:right; padding:5px 8px; color:#000000;">TVA 19,25 %</td>
                            <td class="cell-price">{{ $fmt($tva) }}</td>
                        </tr>
                        @endif

                        <tr class="row-total">
                            <td colspan="3" style="text-align:center; padding:6px 8px; color:#000000;">
                                TOTAL
                            </td>
                            <td class="cell-price">{{ $fmt($total) }}</td>
                        </tr>

                    </tbody>
                </table>

                {{-- MONTANT EN LETTRES --}}
                <div class="arrete" style="color:#000000;">
                    Arrêté le présent DEVIS à la somme de :
                    <strong>{{ $totalEnLettres }}</strong>
                </div>

                {{-- SIGNATURE --}}
                <div class="signature" style="color:#000000; padding-left:60%;">DIRECTEUR</div>

            </td>
        </tr>
    </tbody>

</table>

{{-- ════════ FOOTER (répété sur chaque page via position:fixed) ════════
     Trois divs indépendants : gauche, droite, adresse centrée.
     left:0 / right:0 = bords de la zone contenu (@page margins).
     bottom:-20mm / -26mm = dans la marge basse de 28mm.
════════════════════════════════════════════════════ --}}

<div style="position:fixed; bottom:-20mm; left:0; font-size:9px; line-height:1.6; color:#E31E24;">
    RCN : {{ $company_rccm }}<br>
    <u>N° Cont</u> : {{ $company_niu }} &nbsp;<strong style="color:#E31E24;">SARL</strong>
</div>

<div style="position:fixed; bottom:-20mm; right:0; text-align:right; font-size:9px; line-height:1.6; color:#E31E24;">
    RIB : {{ $company_rib }}<br>
    CNPS : {{ $company_cnps }}
</div>

<div style="position:fixed; bottom:-26mm; left:0; right:0; text-align:center; font-size:9px; line-height:1.6; color:#000000;">
    Sis à {{ $company_address1 }}, {{ $company_address2 }} — {{ $company_city }}
</div>

</body>
</html>
