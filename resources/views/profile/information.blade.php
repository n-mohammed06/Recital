{{-- Modifier son prénom et son nom (ProfileController). --}}
@extends('layouts.recital')

@section('title', 'Tes informations')

@section('content')
    <x-app-page current="profile" :back="route('profile')">
        <div class="dg-compte__intro">
            <h1 class="dg-titre-1">Tes informations</h1>
            <p class="dg-compte__texte">Ton prénom et ton nom, tels qu’ils apparaissent dans ton profil.</p>
        </div>

        <x-error-summary />

        <form class="dg-formulaire" method="POST" action="{{ route('profile.information.update') }}" data-form>
            @csrf
            @method('PUT')

            <div class="dg-duo">
                <x-text-field name="first_name" label="Prénom" :value="$user->first_name" autocomplete="given-name" autocapitalize="words" maxlength="60" required />
                <x-text-field name="last_name" label="Nom" :value="$user->last_name" autocomplete="family-name" autocapitalize="words" maxlength="60" required />
            </div>

            <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Enregistrement…">
                <span class="dg-bouton__texte">Enregistrer</span>
            </button>
        </form>
    </x-app-page>
@endsection
