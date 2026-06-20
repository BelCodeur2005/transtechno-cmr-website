<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine ?? 'Transtechno Cameroun' }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:92%;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;">
                    {{-- En-tête --}}
                    <tr>
                        <td style="background:#004A80;border-bottom:4px solid #E31E24;padding:20px 28px;">
                            <span style="color:#ffffff;font-size:18px;font-weight:bold;letter-spacing:.5px;">TRANSTECHNO CAMEROUN</span><br>
                            <span style="color:#cbd5e1;font-size:11px;">Science technology and medical instrument</span>
                        </td>
                    </tr>
                    {{-- Contenu --}}
                    <tr>
                        <td style="padding:28px;">
                            {!! $slot !!}
                        </td>
                    </tr>
                    {{-- Pied --}}
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:18px 28px;font-size:11px;color:#64748b;line-height:1.6;">
                            <strong style="color:#004A80;">Transtechno Cameroun</strong> — {{ \App\Models\Setting::get('address_line1', 'Village carrefour, entrée bille') }}, {{ \App\Models\Setting::get('address_city', 'Douala, Cameroun') }}<br>
                            Tél : {{ \App\Models\Setting::get('phone_1', '+237 674 64 53 54') }} / {{ \App\Models\Setting::get('phone_2', '+237 699 67 40 36') }} · {{ \App\Models\Setting::get('email', 'office@transtechno-cmr.com') }} · Agréé Minsanté N° 0683
                        </td>
                    </tr>
                </table>
                <p style="color:#94a3b8;font-size:11px;margin:14px 0 0;">Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
            </td>
        </tr>
    </table>
</body>
</html>
