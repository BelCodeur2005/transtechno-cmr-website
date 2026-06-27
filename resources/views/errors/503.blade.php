<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — Transtechno Cameroun</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #004A80;
            padding: 10px 32px;
            font-size: 12px;
            color: #cbd5e1;
            text-align: center;
        }

        .topbar span {
            color: #ffffff;
            font-weight: bold;
        }

        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card {
            background: #ffffff;
            border-top: 5px solid #E31E24;
            border-radius: 4px;
            max-width: 600px;
            width: 100%;
            padding: 48px 40px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .logo-block {
            margin-bottom: 32px;
        }

        .logo-block img {
            max-height: 60px;
            max-width: 220px;
        }

        .logo-fallback {
            font-size: 22px;
            font-weight: bold;
            color: #004A80;
            letter-spacing: 1px;
        }

        .logo-fallback span {
            color: #E31E24;
        }

        .icon {
            width: 72px;
            height: 72px;
            background: #fff4f4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .icon svg {
            width: 36px;
            height: 36px;
            fill: #E31E24;
        }

        h1 {
            font-size: 26px;
            color: #004A80;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        @if(isset($exception) && $exception->getMessage())
        .message-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 16px 20px;
            font-size: 14px;
            color: #334155;
            margin-bottom: 32px;
            text-align: left;
        }
        @endif

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 32px 0;
        }

        .contact-title {
            font-size: 13px;
            font-weight: bold;
            color: #004A80;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }

        .contact-grid {
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .contact-item {
            font-size: 13px;
            color: #475569;
        }

        .contact-item a {
            color: #E31E24;
            text-decoration: none;
            font-weight: bold;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 24px;
        }

        footer {
            background: #002B4B;
            border-top: 3px solid #E31E24;
            text-align: center;
            padding: 16px;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="topbar">
        <span>TRANSTECHNO CAMEROUN</span> — Agréé Minsanté : arrêté N° 0683 — Science technology and medical instrument
    </div>

    <div class="main">
        <div class="card">

            <div class="logo-block">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="/images/logo.png" alt="Transtechno Cameroun">
                @else
                    <div class="logo-fallback">TRANS<span>TECHNO</span></div>
                @endif
            </div>

            <div class="icon">
                <!-- Heroicons solid: wrench-screwdriver -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 6.75a5.25 5.25 0 0 1 6.775-5.025.75.75 0 0 1 .313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 0 1 1.248.313 5.25 5.25 0 0 1-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 1 1 2.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0 1 12 6.75ZM4.117 19.125a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z"/>
                    <path d="m10.076 8.64-2.201-2.2V4.874a.75.75 0 0 0-.364-.643l-3.75-2.25a.75.75 0 0 0-.916.113l-.75.75a.75.75 0 0 0-.113.916l2.25 3.75a.75.75 0 0 0 .643.364h1.564l2.062 2.062 1.575-1.319Z"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="m12.556 17.329 4.204 4.204a3.375 3.375 0 0 0 4.773-4.773l-3.306-3.305a6.803 6.803 0 0 1-1.53.043c-.394-.034-.682-.006-.867.042a.589.589 0 0 0-.167.063l-3.107 3.772Zm3.414-1.36a.75.75 0 0 1 1.06 0l1.875 1.876a.75.75 0 1 1-1.06 1.06L15.97 17.03a.75.75 0 0 1 0-1.061Z"/>
                </svg>
            </div>

            <h1>Site en cours de maintenance</h1>

            <p class="subtitle">
                Nous effectuons des améliorations pour mieux vous servir.<br>
                Le site sera de nouveau disponible très prochainement.
            </p>

            @if(isset($exception) && $exception->getMessage())
            <div class="message-box">
                {{ $exception->getMessage() }}
            </div>
            @endif

            <hr class="divider">

            <p class="contact-title">Besoin urgent ? Contactez-nous directement</p>

            <div class="contact-grid">
                <div class="contact-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#004A80" style="vertical-align:middle;margin-right:4px"><path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C9.39 21 3 14.61 3 7a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z"/></svg>
                    <a href="tel:+237674645354">+237 674 64 53 54</a>
                </div>
                <div class="contact-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#25D366" style="vertical-align:middle;margin-right:4px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347Z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.116.549 4.107 1.515 5.833L.057 23.215a.75.75 0 0 0 .922.922l5.382-1.458A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0Zm0 22a9.944 9.944 0 0 1-5.072-1.384l-.364-.216-3.773 1.023 1.023-3.773-.216-.364A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Z"/></svg>
                    <a href="https://wa.me/237699674036">+237 699 67 40 36</a>
                </div>
                <div class="contact-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#004A80" style="vertical-align:middle;margin-right:4px"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
                    <a href="mailto:office@transtechno-cmr.com">office@transtechno-cmr.com</a>
                </div>
            </div>

            <div class="badge">Certifié OMS Qualité · Agréé Minsanté</div>

        </div>
    </div>

    <footer>
        © {{ date('Y') }} TRANSTECHNO CAMEROUN — Douala, Cameroun
    </footer>

</body>
</html>
