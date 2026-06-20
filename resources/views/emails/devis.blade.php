@component('emails.layout', ['subjectLine' => 'Votre devis #' . $quotation->reference])
    <h1 style="margin:0 0 14px;font-size:20px;color:#004A80;">Bonjour {{ $quotation->responsible_name }},</h1>

    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">
        Veuillez trouver ci-joint votre devis <strong>#{{ $quotation->reference }}</strong> établi pour
        <strong>{{ $quotation->structure_name }}</strong>, conformément à votre demande.
    </p>

    @if (! empty($customMessage))
        <div style="background:#f8fafc;border-left:3px solid #3a6090;padding:12px 14px;font-size:13px;line-height:1.6;color:#334155;white-space:pre-line;margin:0 0 18px;">{{ $customMessage }}</div>
    @endif

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 18px;">
        <tr>
            <td style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;">
                <span style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Total du devis</span><br>
                <span style="font-size:22px;font-weight:bold;color:#E31E24;">{{ number_format($quotation->totalTtc(), 0, ',', ' ') }} FCFA</span><br>
                <span style="font-size:11px;color:#64748b;">Validité : 30 jours · {{ $quotation->vat_exempt ? 'Exonération TVA MINSANTÉ' : 'TVA 19,25 % incluse' }}</span>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">
        Le détail complet (articles, prix unitaires, conditions) figure dans le document PDF joint. Pour valider ce devis,
        retournez-le signé avec la mention « Bon pour accord », ou contactez votre conseiller.
    </p>

    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">
        Pour toute question : <strong style="color:#004A80;">+237 674 64 53 54</strong>.
    </p>

    <p style="margin:0;font-size:14px;color:#334155;">Cordialement,<br><strong style="color:#004A80;">L'équipe commerciale Transtechno Cameroun</strong></p>
@endcomponent
