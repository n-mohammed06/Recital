{{--
    Mise en page de l'espace des élèves connectés : en-tête en haut, onglets en bas, à portée de pouce
    (comme dans la maquette). Les onglets Parcours et Piano viendront avec les leçons.

    Utilisation : <x-app-page current="profile">…</x-app-page>
    current : onglet actif (« home » ou « profile »).
    back    : adresse du bouton « Retour » (facultatif). Sans lui, le logo ramène à l'accueil.
--}}
@props([
    'current' => null,
    'back' => null,
])

@php
    $tabs = [
        'home' => ['label' => 'Accueil', 'url' => route('home'), 'icon' => '<path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>'],
        'profile' => ['label' => 'Profil', 'url' => route('profile'), 'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>'],
    ];
@endphp

<div class="dg-espace">
    <header class="dg-espace__haut">
        @if ($back)
            <a class="dg-retour" href="{{ $back }}">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7"/></svg>
                Retour
            </a>
        @else
            <a class="dg-logo-lien" href="{{ route('home') }}" aria-label="Récital, accueil"><x-logo /></a>
        @endif
    </header>

    <div class="dg-espace__contenu">
        {{ $slot }}
    </div>

    <nav class="dg-nav" aria-label="Navigation principale">
        @foreach ($tabs as $key => $tab)
            <a class="dg-onglet" href="{{ $tab['url'] }}" @if ($current === $key) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">{!! $tab['icon'] !!}</svg>
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
