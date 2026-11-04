{{-- Changer de mot de passe depuis le profil (Auth\PasswordController). --}}
@extends('layouts.recital')

@section('title', 'Changer de mot de passe')

@section('content')
    <x-app-page current="profile" :back="route('profile')">
        <div class="dg-compte__intro">
            <h1 class="dg-titre-1">Changer de mot de passe</h1>
            <p class="dg-compte__texte">Tes autres appareils seront déconnectés. Celui-ci reste connecté.</p>
        </div>

        <x-error-summary />

        <form class="dg-formulaire" method="POST" action="{{ route('profile.password.update') }}" data-form>
            @csrf
            @method('PUT')

            {{-- Indique au gestionnaire de mots de passe du téléphone pour quel compte enregistrer le nouveau. --}}
            <input type="email" value="{{ $user->email }}" autocomplete="username" hidden>

            <x-password-field name="current_password" label="Mot de passe actuel" autocomplete="current-password" required />
            <x-password-field label="Nouveau mot de passe" autocomplete="new-password" :rules="true" minlength="8" required />

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Enregistrement…">
                <span class="dg-bouton__texte">Changer mon mot de passe</span>
            </button>
        </form>

        <p class="dg-mention">Mot de passe actuel oublié&nbsp;? Déconnecte-toi, puis choisis «&nbsp;Mot de passe oublié&nbsp;» sur la page de connexion.</p>
    </x-app-page>
@endsection
