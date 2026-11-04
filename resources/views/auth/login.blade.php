@extends('layouts.recital')

@section('title', 'Connexion')
@section('theme-color', '#1C0806')

@section('content')
    <x-piano-scene :big="true" sr-prefix="Connexion à">
        @if (session('status'))
            <x-alert :title="session('status')" />
        @endif

        {{-- Mauvais identifiants ou rafale d'essais : un seul message, en haut du formulaire. --}}
        @error('login')
            <x-alert type="erreur" :title="$message" tabindex="-1" data-error-summary />
        @enderror

        <form class="dg-formulaire" method="POST" action="{{ route('login') }}" data-form>
            @csrf

            <x-text-field
                name="email"
                label="Adresse e-mail"
                type="email"
                icon="mail"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                maxlength="255"
                required
            />

            <x-password-field autocomplete="current-password" required />

            <div class="dg-options">
                <x-checkbox name="remember">Se souvenir de moi</x-checkbox>
                <a class="dg-lien" href="{{ route('password.request') }}">Mot de passe oublié&nbsp;?</a>
            </div>

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Connexion…">
                <span class="dg-bouton__texte">Se connecter</span>
                <svg class="dg-bouton__fleche" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
        </form>

        <p class="dg-bascule">Pas encore de compte&nbsp;? <a class="dg-lien dg-lien--fort" href="{{ route('register') }}">Créer un compte</a></p>

        <x-legal-links />
    </x-piano-scene>
@endsection
