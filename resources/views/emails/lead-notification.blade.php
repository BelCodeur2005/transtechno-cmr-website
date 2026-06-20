@component('emails.layout', ['subjectLine' => $subjectLine])
    <p style="margin:0 0 6px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;color:#E31E24;">Nouvelle demande</p>
    <h1 style="margin:0 0 14px;font-size:20px;color:#004A80;">{{ $heading }}</h1>
    <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#334155;">{{ $intro }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
        @foreach ($rows as $label => $value)
            <tr>
                <td style="padding:9px 14px;background:#f8fafc;font-size:12px;color:#64748b;width:38%;border-bottom:1px solid #eef2f7;">{{ $label }}</td>
                <td style="padding:9px 14px;font-size:13px;color:#1f2937;border-bottom:1px solid #eef2f7;">{{ $value ?: '—' }}</td>
            </tr>
        @endforeach
    </table>

    @if (! empty($messageBody))
        <p style="margin:18px 0 6px;font-size:12px;font-weight:bold;color:#3a6090;">Message :</p>
        <div style="background:#f8fafc;border-left:3px solid #3a6090;padding:12px 14px;font-size:13px;line-height:1.6;color:#334155;white-space:pre-line;">{{ $messageBody }}</div>
    @endif

    @if (! empty($actionUrl))
        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:22px 0 4px;">
            <tr>
                <td style="background:#E31E24;border-radius:8px;">
                    <a href="{{ $actionUrl }}" style="display:inline-block;padding:11px 22px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">{{ $actionLabel ?? 'Ouvrir dans le back-office' }}</a>
                </td>
            </tr>
        </table>
    @endif
@endcomponent
