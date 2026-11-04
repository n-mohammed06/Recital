<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    {{-- Thème Nuit : évite un flash blanc pendant le chargement. --}}
    <meta name="color-scheme" content="dark">
    {{-- Couleur de la barre du téléphone : celle du haut de la page. --}}
    <meta name="theme-color" content="@yield('theme-color', '#22113A')">
    <title>@yield('title') · Récital</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    {{-- Polices et styles servis par le site lui-même : aucun appel à Google ou à un autre service. --}}
    <link rel="preload" href="{{ asset('fonts/atkinson-hyperlegible-next.woff') }}" as="font" type="font/woff" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/recital.css') }}">
    <script src="{{ asset('js/recital.js') }}" defer></script>
</head>
<body>
    <main class="dg-app">
        @yield('content')
    </main>
</body>
</html>
