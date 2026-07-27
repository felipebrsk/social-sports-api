{{-- resources/views/auth/email-confirmado.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail confirmado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #F6F4EE;
            --ink: #142118;
            --ink-soft: #4B584F;
            --field: #1E6B45;
            --field-dark: #123D28;
            --gold: #E3A73C;
            --line: #DAD5C7;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            background-image:
                repeating-linear-gradient(180deg, rgba(20, 33, 24, 0.035) 0px, rgba(20, 33, 24, 0.035) 1px, transparent 1px, transparent 64px);
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            text-align: center;
            padding: 48px 32px 40px;
            position: relative;
        }

        .ribbon-wrap {
            position: relative;
            height: 132px;
            margin-bottom: 8px;
        }

        .ribbon {
            position: absolute;
            top: 46px;
            height: 34px;
            width: 50%;
            background: repeating-linear-gradient(135deg, var(--field) 0 14px, var(--field-dark) 14px 28px);
            opacity: 0.9;
        }

        .ribbon--left {
            left: -8px;
            clip-path: polygon(0 0, 100% 0, 86% 100%, 0% 100%);
        }

        .ribbon--right {
            right: -8px;
            clip-path: polygon(14% 0, 100% 0, 100% 100%, 0% 100%);
        }

        .badge {
            position: absolute;
            left: 50%;
            top: 6px;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--field);
            border: 4px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(18, 61, 40, 0.28);
            animation: pop 0.5s cubic-bezier(.2, .9, .3, 1.3) both;
        }

        .badge svg {
            width: 54px;
            height: 54px;
        }

        @keyframes pop {
            0% {
                transform: translateX(-50%) scale(0.6);
                opacity: 0;
            }

            100% {
                transform: translateX(-50%) scale(1);
                opacity: 1;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .badge {
                animation: none;
            }
        }

        .eyebrow {
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--field);
            margin: 0 0 10px;
        }

        h1 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 30px;
            letter-spacing: 0.01em;
            text-transform: uppercase;
            margin: 0 0 14px;
            color: var(--ink);
        }

        p.lead {
            font-size: 15px;
            line-height: 1.6;
            color: var(--ink-soft);
            margin: 0 0 32px;
        }

        p.lead strong {
            color: var(--ink);
            font-weight: 600;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            background: var(--field);
            color: #fff;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .btn:hover {
            background: var(--field-dark);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn:focus-visible {
            outline: 3px solid var(--gold);
            outline-offset: 2px;
        }

        .divider {
            width: 40px;
            height: 3px;
            background: var(--line);
            border-radius: 2px;
            margin: 28px auto;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="ribbon-wrap">
            <div class="ribbon ribbon--left"></div>
            <div class="ribbon ribbon--right"></div>
            <div class="badge">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 13L10 18L19 7" stroke="#F6F4EE" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <p class="eyebrow">Conta verificada</p>
        <h1>E-mail confirmado</h1>
        <p class="lead">
            Você cruzou a linha de chegada. Sua conta já está pronta
            @if(! empty($user) && !empty($user->name))
            , <strong>{{ $user->name }}</strong>
            @endif
            para entrar em campo.
        </p>

        <a href="{{ Config::get('app.frontend_url') }}" class="btn">
            Voltar para o início
        </a>

        <div class="divider"></div>
    </div>
</body>

</html>