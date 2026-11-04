@extends('layouts.recital')

@section('title', 'Créer un compte')
@section('theme-color', '#1C0806')

@section('content')
    <x-piano-scene :big="true" sr-prefix="Inscription à">
        <x-error-summary />

        <form class="dg-formulaire" method="POST" action="{{ route('register') }}" data-form>
            @csrf

            <div class="dg-duo">
                <x-text-field name="first_name" label="Prénom" autocomplete="given-name" autocapitalize="words" maxlength="60" required />
                <x-text-field name="last_name" label="Nom" autocomplete="family-name" autocapitalize="words" maxlength="60" required />
            </div>

            <x-text-field
                name="email"
                label="Adresse e-mail"
                type="email"
                icon="mail"
                hint="Ton adresse personnelle. Tu y recevras un lien pour la confirmer."
                autocomplete="email"
                autocapitalize="none"
                spellcheck="false"
                maxlength="255"
                required
            />

            <x-password-field label="Mot de passe" autocomplete="new-password" :rules="true" minlength="8" required />

            <div class="dg-cases">
                <x-checkbox name="terms" required>
                    J’accepte les <a class="dg-lien" href="{{ route('legal.terms') }}" target="_blank" rel="noopener">conditions d’utilisation<span class="dg-visuellement-cache"> (s’ouvre dans un nouvel onglet)</span></a>.
                </x-checkbox>

                <x-checkbox name="age" required>
                    J’ai 15 ans ou plus, ou l’un de mes parents est d’accord pour que je crée ce compte.
                </x-checkbox>
            </div>

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Création du compte…">
                <span class="dg-bouton__texte">Créer mon compte</span>
                <svg class="dg-bouton__fleche" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>

            <p class="dg-mention">
                Ton prénom, ton nom et ton adresse e-mail servent uniquement à gérer ton compte. Ils ne sont ni vendus ni partagés.
                <a class="dg-lien" href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">Politique de confidentialité<span class="dg-visuellement-cache"> (s’ouvre dans un nouvel onglet)</span></a>
            </p>
        </form>

        <p class="dg-bascule">Déjà un compte&nbsp;? <a class="dg-lien dg-lien--fort" href="{{ route('login') }}">Se connecter</a></p>

        <x-legal-links />
    </x-piano-scene>
@endsection
