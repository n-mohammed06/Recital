{{-- Conditions d'utilisation et politique de confidentialité : le texte vient de resources/legal/*.md --}}
@extends('layouts.recital')

@section('title', $title)

@section('content')
    <div class="dg-compte">
        <header class="dg-compte__haut">
            <a class="dg-logo-lien" href="{{ route('home') }}" aria-label="Récital, retour à l’accueil"><x-logo /></a>
        </header>

        <article class="dg-texte-legal">
            <h1 class="dg-titre-1">{{ $title }}</h1>
            {!! $content !!}
        </article>

        <x-legal-links />
    </div>
@endsection
