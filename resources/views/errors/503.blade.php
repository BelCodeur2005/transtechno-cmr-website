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
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
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
                    📞 <a href="tel:+237674645354">+237 674 64 53 54</a>
                </div>
                <div class="contact-item">
                    💬 <a href="https://wa.me/237699674036">+237 699 67 40 36</a>
                </div>
                <div class="contact-item">
                    ✉️ <a href="mailto:office@transtechno-cmr.com">office@transtechno-cmr.com</a>
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
