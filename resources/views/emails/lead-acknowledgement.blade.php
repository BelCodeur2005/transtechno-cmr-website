@component('emails.layout', ['subjectLine' => $subjectLine])
    <h1 style="margin:0 0 14px;font-size:20px;color:#004A80;">Bonjour {{ $recipientName }},</h1>
    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">{{ $intro }}</p>

    @if (! empty($reference))
        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 18px;">
            <tr>
                <td style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 18px;">
                    <span style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Votre référence de suivi</span><br>
                    <span style="font-size:20px;font-weight:bold;color:#E31E24;">#{{ $reference }}</span>
                </td>
            </tr>
        </table>
    @endif

    @if (! empty($details))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin:0 0 18px;">
            @foreach ($details as $label => $value)
                <tr>
                    <td style="padding:9px 14px;background:#f8fafc;font-size:12px;color:#64748b;width:40%;border-bottom:1px solid #eef2f7;">{{ $label }}</td>
                    <td style="padding:9px 14px;font-size:13px;color:#1f2937;border-bottom:1px solid #eef2f7;">{{ $value ?: '—' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334155;">
        Notre équipe commerciale revient vers vous sous <strong>24h à 48h</strong>. Pour toute urgence,
        contactez-nous au <strong style="color:#004A80;">+237 674 64 53 54</strong>.
    </p>
    <p style="margin:0;font-size:14px;color:#334155;">Cordialement,<br><strong style="color:#004A80;">L'équipe Transtechno Cameroun</strong></p>
@endcomponent
