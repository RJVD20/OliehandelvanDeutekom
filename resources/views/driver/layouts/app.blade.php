<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/favicon-v2.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#071d35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Routeflow">
    <title>@yield('title', 'Mijn route') | Kachelvloeistof.nl</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&family=manrope:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --driver-navy: #071d35;
            --driver-navy-light: #0d2c4d;
            --driver-ink: #10263d;
            --driver-muted: #6b7a8b;
            --driver-bg: #f2f5f8;
            --driver-card: #ffffff;
            --driver-border: #e1e7ed;
            --driver-gold: #e2a925;
            --driver-gold-dark: #c98f0b;
            --driver-green: #2ba66f;
            --driver-shadow: 0 18px 50px -32px rgba(7, 29, 53, .35);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--driver-ink);
            font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at 5% 0%, rgba(226, 169, 37, .09), transparent 22rem),
                linear-gradient(180deg, #eef3f7 0, var(--driver-bg) 28rem);
        }
        button, input, select { font: inherit; }
        a { color: inherit; text-decoration: none; }
        svg { width: 1.25rem; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.8; }
        .driver-shell { width: min(100% - 2rem, 1280px); margin: 0 auto; padding: 1.25rem 0 4rem; }
        .driver-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.4rem; }
        .driver-brand { display: flex; align-items: center; gap: .7rem; }
        .driver-brand__mark { display: grid; width: 2.8rem; height: 2.8rem; place-items: center; color: var(--driver-gold); border-radius: .85rem; background: var(--driver-navy); box-shadow: 0 8px 22px -12px var(--driver-navy); }
        .driver-brand__mark svg { width: 1.5rem; }
        .driver-brand strong, .driver-brand small { display: block; }
        .driver-brand strong { font: 800 1.1rem/1.1 Manrope, sans-serif; letter-spacing: -.035em; }
        .driver-brand strong span { color: var(--driver-gold-dark); }
        .driver-brand small { margin-top: .18rem; color: var(--driver-muted); font-size: .66rem; letter-spacing: .08em; text-transform: uppercase; }
        .driver-profile { display: flex; align-items: center; gap: .7rem; }
        .driver-profile__copy { text-align: right; }
        .driver-profile__copy span, .driver-profile__copy strong { display: block; }
        .driver-profile__copy span { color: var(--driver-muted); font-size: .68rem; text-transform: uppercase; letter-spacing: .1em; }
        .driver-profile__copy strong { font-size: .88rem; }
        .driver-avatar { display: grid; width: 2.65rem; height: 2.65rem; place-items: center; color: white; font-weight: 700; border: 3px solid white; border-radius: 50%; background: linear-gradient(135deg, var(--driver-navy-light), var(--driver-navy)); box-shadow: 0 6px 20px -10px var(--driver-navy); }

        .driver-hero { position: relative; display: flex; align-items: end; justify-content: space-between; gap: 2rem; overflow: hidden; padding: 2.25rem 2.4rem; color: white; border-radius: 1.4rem; background: var(--driver-navy); box-shadow: 0 25px 65px -38px rgba(7, 29, 53, .8); }
        .driver-hero::before { position: absolute; inset: auto auto -7rem 48%; width: 24rem; height: 24rem; content: ""; border: 1px solid rgba(255,255,255,.06); border-radius: 50%; box-shadow: 0 0 0 4rem rgba(255,255,255,.025), 0 0 0 8rem rgba(255,255,255,.02); }
        .driver-hero__copy, .driver-filter { position: relative; z-index: 1; }
        .driver-eyebrow { display: flex; align-items: center; gap: .5rem; margin: 0 0 .5rem; color: var(--driver-gold); font-size: .67rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .driver-eyebrow > span { width: 1.1rem; height: 2px; background: currentColor; }
        .driver-hero h1 { margin: 0; font: 800 clamp(1.8rem, 4vw, 2.8rem)/1.1 Manrope, sans-serif; letter-spacing: -.045em; }
        .driver-hero__copy > p:last-child { margin: .65rem 0 0; color: #b8c6d4; font-size: .92rem; }
        .driver-filter { display: flex; align-items: end; gap: .65rem; padding: .7rem; border: 1px solid rgba(255,255,255,.11); border-radius: 1rem; background: rgba(255,255,255,.07); backdrop-filter: blur(10px); }
        .driver-filter label { display: grid; gap: .3rem; }
        .driver-filter label > span { padding-left: .15rem; color: #a9b9c9; font-size: .62rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
        .driver-filter input, .driver-filter select { height: 2.7rem; min-width: 9rem; padding: 0 .75rem; color: white; border: 1px solid rgba(255,255,255,.13); border-radius: .65rem; outline: none; background: rgba(0,0,0,.18); color-scheme: dark; }
        .driver-filter select { min-width: 8rem; }
        .driver-filter input:focus, .driver-filter select:focus { border-color: var(--driver-gold); box-shadow: 0 0 0 3px rgba(226,169,37,.15); }
        .driver-filter button { display: flex; align-items: center; gap: .35rem; height: 2.7rem; padding: 0 1rem; color: var(--driver-navy); white-space: nowrap; border: 0; border-radius: .65rem; background: var(--driver-gold); font-size: .78rem; font-weight: 700; cursor: pointer; }
        .driver-filter button:hover { background: #efbb43; transform: translateY(-1px); }
        .driver-filter button svg { width: .95rem; }

        .driver-overview { display: grid; grid-template-columns: minmax(0, 1fr) 180px 180px; gap: 1rem; margin: 1rem 0 2.2rem; }
        .driver-progress-card, .driver-stat { border: 1px solid var(--driver-border); border-radius: 1rem; background: var(--driver-card); box-shadow: var(--driver-shadow); }
        .driver-progress-card { padding: 1rem 1.2rem; }
        .driver-progress-card__top { display: flex; align-items: center; justify-content: space-between; }
        .driver-progress-card__top span, .driver-progress-card__top strong { display: block; }
        .driver-progress-card__top span { color: var(--driver-muted); font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; }
        .driver-progress-card__top strong { margin-top: .1rem; font-size: .9rem; }
        .driver-progress-card__percent { color: var(--driver-green); font: 800 1.35rem Manrope, sans-serif !important; }
        .driver-progress { height: .42rem; overflow: hidden; margin-top: .7rem; border-radius: 999px; background: #e9eef2; }
        .driver-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--driver-green), #55c990); transition: width .5s ease; }
        .driver-stat { display: flex; align-items: center; gap: .75rem; padding: .85rem 1rem; }
        .driver-stat__icon { display: grid; width: 2.55rem; height: 2.55rem; flex: 0 0 auto; place-items: center; border-radius: .75rem; }
        .driver-stat__icon--open { color: var(--driver-gold-dark); background: #fff6dd; }
        .driver-stat__icon--done { color: var(--driver-green); background: #e7f7f0; }
        .driver-stat__icon svg { width: 1.25rem; }
        .driver-stat small, .driver-stat strong { display: block; }
        .driver-stat small { color: var(--driver-muted); font-size: .7rem; }
        .driver-stat strong { font: 800 1.25rem Manrope, sans-serif; }

        .driver-content { display: grid; grid-template-columns: minmax(0, 1fr) 310px; gap: 2.2rem; align-items: start; }
        .driver-section-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1.2rem; }
        .driver-section-heading h2 { margin: 0; font: 800 1.65rem Manrope, sans-serif; letter-spacing: -.04em; }
        .driver-section-heading .driver-eyebrow { margin-bottom: .2rem; color: var(--driver-gold-dark); }
        .driver-route-count { padding: .35rem .65rem; color: var(--driver-muted); border: 1px solid var(--driver-border); border-radius: 999px; background: white; font-size: .72rem; font-weight: 600; }
        .driver-stop { display: grid; grid-template-columns: 2.7rem minmax(0, 1fr); }
        .driver-stop__rail { display: flex; flex-direction: column; align-items: center; }
        .driver-stop__number { z-index: 1; display: grid; width: 2.25rem; height: 2.25rem; flex: 0 0 auto; place-items: center; color: white; border: 4px solid var(--driver-bg); border-radius: 50%; background: var(--driver-navy); font-size: .75rem; font-weight: 700; box-sizing: content-box; }
        .driver-stop__number svg { width: 1rem; }
        .driver-stop--completed .driver-stop__number { background: var(--driver-green); }
        .driver-stop__rail i { width: 2px; min-height: 2rem; flex: 1; margin: -.15rem 0; background: #d8e0e7; }
        .driver-stop__card { margin-bottom: 1rem; padding: 1.25rem; border: 1px solid var(--driver-border); border-radius: 1rem; background: white; box-shadow: var(--driver-shadow); transition: border .2s, transform .2s, box-shadow .2s; }
        .driver-stop__card:hover { border-color: #c7d2dc; transform: translateY(-2px); box-shadow: 0 24px 54px -34px rgba(7, 29, 53, .45); }
        .driver-stop--completed .driver-stop__card { opacity: .72; box-shadow: none; }
        .driver-stop__head { display: flex; align-items: start; justify-content: space-between; gap: 1rem; }
        .driver-stop__label { color: var(--driver-muted); font-size: .66rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
        .driver-stop__head h3 { margin: .2rem 0 0; font: 700 1.05rem Manrope, sans-serif; letter-spacing: -.02em; }
        .driver-status { display: inline-flex; align-items: center; gap: .35rem; padding: .28rem .55rem; color: #9a6a00; border-radius: 999px; background: #fff5d8; font-size: .65rem; font-weight: 700; }
        .driver-status i { width: .4rem; height: .4rem; border-radius: 50%; background: var(--driver-gold); }
        .driver-status--done { color: #187c52; background: #e8f7f0; }
        .driver-status--done i { background: var(--driver-green); }
        .driver-address { display: flex; gap: .65rem; margin-top: 1rem; color: var(--driver-muted); }
        .driver-address > span { display: grid; width: 2rem; height: 2rem; flex: 0 0 auto; place-items: center; color: var(--driver-navy-light); border-radius: .6rem; background: #edf3f7; }
        .driver-address svg { width: 1.05rem; }
        .driver-address p { margin: 0; font-size: .83rem; line-height: 1.55; }
        .driver-address strong { color: var(--driver-ink); font-weight: 600; }
        .driver-note { display: flex; gap: .65rem; margin-top: 1rem; padding: .75rem; color: #72510b; border-left: 3px solid var(--driver-gold); border-radius: .2rem .65rem .65rem .2rem; background: #fff9e9; }
        .driver-note svg { width: 1rem; flex: 0 0 auto; margin-top: .1rem; }
        .driver-note p, .driver-note strong { display: block; margin: 0; font-size: .75rem; }
        .driver-note strong { margin-bottom: .1rem; }
        .driver-stop__actions { display: flex; gap: .6rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #edf0f3; }
        .driver-stop__actions form { flex: 1; }
        .driver-button { display: inline-flex; height: 2.65rem; align-items: center; justify-content: center; gap: .45rem; padding: 0 1rem; border-radius: .65rem; font-size: .76rem; font-weight: 700; cursor: pointer; transition: .18s ease; }
        .driver-button svg { width: 1rem; }
        .driver-button--nav { color: white; background: var(--driver-navy); }
        .driver-button--nav:hover { background: var(--driver-navy-light); transform: translateY(-1px); }
        .driver-button--complete { width: 100%; color: #176d49; border: 1px solid #bfe5d4; background: #edfaf4; }
        .driver-button--complete:hover { border-color: var(--driver-green); background: #e0f6ec; transform: translateY(-1px); }

        .driver-sidebar { position: sticky; top: 1rem; display: grid; gap: 1rem; }
        .driver-route-card { position: relative; overflow: hidden; padding: 1.5rem; color: white; border-radius: 1.2rem; background: linear-gradient(145deg, #0c3155, var(--driver-navy)); box-shadow: 0 22px 55px -34px var(--driver-navy); }
        .driver-route-card::after { position: absolute; right: -3rem; bottom: -4rem; width: 10rem; height: 10rem; content: ""; border: 1px solid rgba(255,255,255,.07); border-radius: 50%; box-shadow: 0 0 0 2.5rem rgba(255,255,255,.025); }
        .driver-route-card__icon { display: grid; width: 3rem; height: 3rem; place-items: center; margin-bottom: 1.5rem; color: var(--driver-gold); border: 1px solid rgba(255,255,255,.1); border-radius: .85rem; background: rgba(255,255,255,.07); }
        .driver-route-card__icon svg { width: 1.45rem; }
        .driver-route-card .driver-eyebrow { position: relative; z-index: 1; }
        .driver-route-card h2 { position: relative; z-index: 1; margin: .3rem 0 .5rem; font: 700 1.3rem Manrope, sans-serif; }
        .driver-route-card > p:not(.driver-eyebrow) { position: relative; z-index: 1; margin: 0; color: #aebdca; font-size: .8rem; line-height: 1.6; }
        .driver-route-button { position: relative; z-index: 1; display: flex; height: 3rem; align-items: center; justify-content: center; gap: .5rem; margin-top: 1.4rem; color: var(--driver-navy); border-radius: .7rem; background: var(--driver-gold); font-size: .8rem; font-weight: 800; transition: .18s ease; }
        .driver-route-button:hover { background: #efbb43; transform: translateY(-2px); }
        .driver-route-button--disabled { opacity: .4; pointer-events: none; }
        .driver-planner-link { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: .8rem; padding: 1rem; border: 1px solid var(--driver-border); border-radius: 1rem; background: white; box-shadow: var(--driver-shadow); }
        .driver-planner-link > span:first-child { display: grid; width: 2.5rem; height: 2.5rem; place-items: center; color: var(--driver-navy); border-radius: .7rem; background: #edf3f7; }
        .driver-planner-link svg { width: 1.15rem; }
        .driver-planner-link strong, .driver-planner-link small { display: block; }
        .driver-planner-link strong { font-size: .82rem; }
        .driver-planner-link small { margin-top: .12rem; color: var(--driver-muted); font-size: .68rem; }
        .driver-planner-link > svg { color: #9ba9b6; transition: transform .18s; }
        .driver-planner-link:hover > svg { transform: translateX(3px); }
        .driver-empty { padding: 3rem 1rem; text-align: center; border: 1px dashed #cbd5de; border-radius: 1rem; background: rgba(255,255,255,.5); }
        .driver-empty > span { display: grid; width: 3.5rem; height: 3.5rem; place-items: center; margin: 0 auto 1rem; color: var(--driver-muted); border-radius: 1rem; background: white; }
        .driver-empty h3 { margin: 0; font: 700 1rem Manrope, sans-serif; }
        .driver-empty p { margin: .35rem 0 0; color: var(--driver-muted); font-size: .8rem; }
        .driver-toast { display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; padding: .8rem 1rem; color: #176d49; border: 1px solid #bfe5d4; border-radius: .85rem; background: #edfaf4; box-shadow: var(--driver-shadow); }
        .driver-toast > span { display: grid; width: 2rem; height: 2rem; place-items: center; color: white; border-radius: 50%; background: var(--driver-green); }
        .driver-toast svg { width: 1rem; }
        .driver-toast strong, .driver-toast small { display: block; }
        .driver-toast strong { font-size: .8rem; }
        .driver-toast small { margin-top: .1rem; font-size: .7rem; }
        .app-fade-in { animation: appFade .45s ease-out both; }
        @keyframes appFade { from { opacity: 0; transform: translateY(7px); } to { opacity: 1; transform: none; } }

        @media (max-width: 900px) {
            .driver-hero { align-items: stretch; flex-direction: column; }
            .driver-filter { width: 100%; }
            .driver-filter label { flex: 1; }
            .driver-filter input, .driver-filter select { width: 100%; }
            .driver-overview { grid-template-columns: 1fr 1fr; }
            .driver-progress-card { grid-column: 1 / -1; }
            .driver-content { grid-template-columns: 1fr; }
            .driver-sidebar { position: static; grid-row: 1; grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .driver-shell { width: min(100% - 1rem, 1280px); padding-top: .7rem; }
            .driver-profile__copy { display: none; }
            .driver-brand small { display: none; }
            .driver-hero { padding: 1.5rem 1.15rem; border-radius: 1.1rem; }
            .driver-hero__copy > p:last-child { line-height: 1.5; }
            .driver-filter { align-items: stretch; flex-direction: column; padding: .65rem; }
            .driver-filter label { width: 100%; }
            .driver-filter input, .driver-filter select { min-width: 0; }
            .driver-filter button { justify-content: center; margin-top: .15rem; }
            .driver-overview { gap: .6rem; margin-bottom: 1.8rem; }
            .driver-progress-card { padding: .9rem; }
            .driver-stat { gap: .5rem; padding: .7rem; }
            .driver-stat__icon { width: 2.2rem; height: 2.2rem; }
            .driver-sidebar { grid-template-columns: 1fr; }
            .driver-route-card { padding: 1.25rem; }
            .driver-section-heading { margin-top: .4rem; }
            .driver-stop { grid-template-columns: 2.35rem minmax(0, 1fr); }
            .driver-stop__number { width: 1.85rem; height: 1.85rem; border-width: 3px; }
            .driver-stop__card { padding: 1rem; }
            .driver-stop__head { gap: .5rem; }
            .driver-stop__head h3 { font-size: .95rem; }
            .driver-status { padding-inline: .45rem; }
            .driver-stop__actions { align-items: stretch; flex-direction: column; }
            .driver-stop__actions form { width: 100%; }
            .driver-button { width: 100%; height: 2.9rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body>
    <main style="padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px);">
        @yield('content')
    </main>
</body>
</html>
