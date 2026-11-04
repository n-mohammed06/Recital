{{-- Accueil provisoire après la connexion : les leçons viendront ici. --}}
@extends('layouts.recital')

@section('title', 'Accueil')

@section('content')
    @php($user = auth()->user())

    <x-app-page current="home">
        @if (session('status'))
            <x-alert :title="session('status')" />
        @endif

        <div class="dg-compte__intro">
            <p class="dg-surtitre">Ton espace</p>
            <h1 class="dg-titre-1">Bonjour {{ $user->first_name }}</h1>
            <p class="dg-compte__texte">Ton compte est prêt. Les leçons arriveront ici dans la prochaine étape du projet.</p>
        </div>

        <a class="dg-carte dg-carte--lien" href="{{ route('profile') }}">
            <span class="dg-carte__corps">
                <span class="dg-carte__titre">Ton profil</span>
                <span class="dg-texte-doux">Tes progrès, tes réglages et tes informations.</span>
            </span>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7"/></svg>
        </a>

        <x-legal-links />
    </x-app-page>
@endsection
