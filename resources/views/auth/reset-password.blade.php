@extends('layouts.recital')

@section('title', 'Nouveau mot de passe')

@section('content')
    <x-piano-scene title="Nouveau mot de passe">
        <p class="dg-texte-doux">Choisis ton nouveau mot de passe. Tes autres appareils seront déconnectés.</p>

        <x-error-summary />

        <form class="dg-formulaire" method="POST" action="{{ route('password.update') }}" data-form>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- L'adresse aide le gestionnaire de mots de passe du téléphone à enregistrer le nouveau. --}}
            <x-text-field
                name="email"
                label="Adresse e-mail"
                type="email"
                icon="mail"
                :value="$email"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                maxlength="255"
                required
            />

            <x-password-field label="Nouveau mot de passe" autocomplete="new-password" :rules="true" minlength="8" required />

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Enregistrement…">
                <span class="dg-bouton__texte">Enregistrer</span>
                <svg class="dg-bouton__fleche" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
        </form>
    </x-piano-scene>
@endsection
