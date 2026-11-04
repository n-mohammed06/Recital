@extends('layouts.recital')

@section('title', 'Mot de passe oublié')

@section('content')
    <x-piano-scene title="Mot de passe oublié">
        <p class="dg-texte-doux">Indique l’adresse e-mail de ton compte&nbsp;: tu recevras un lien pour choisir un nouveau mot de passe.</p>

        @if (session('status'))
            <x-alert :title="session('status')" />
        @endif

        <form class="dg-formulaire" method="POST" action="{{ route('password.email') }}" data-form>
            @csrf

            <x-text-field
                name="email"
                label="Adresse e-mail"
                type="email"
                icon="mail"
                autocomplete="email"
                autocapitalize="none"
                spellcheck="false"
                maxlength="255"
                required
            />

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Envoi…">
                <span class="dg-bouton__texte">Recevoir le lien</span>
                <svg class="dg-bouton__fleche" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
        </form>

        <p class="dg-bascule"><a class="dg-lien dg-lien--fort" href="{{ route('login') }}">Retour à la connexion</a></p>
    </x-piano-scene>
@endsection
