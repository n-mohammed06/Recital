{{-- Choisir son avatar parmi les dessins de Récital, ou garder ses initiales (ProfileController). --}}
@extends('layouts.recital')

@section('title', 'Ton avatar')

@section('content')
    @php($current = (string) old('avatar', $user->chosenAvatar()))

    <x-app-page current="profile" :back="route('profile')">
        <div class="dg-compte__intro">
            <h1 class="dg-titre-1">Ton avatar</h1>
            <p class="dg-compte__texte">Choisis le dessin qui t’accompagne dans ton profil.</p>
        </div>

        <x-error-summary />

        <form class="dg-formulaire" method="POST" action="{{ route('profile.avatar.update') }}" data-form>
            @csrf
            @method('PUT')

            {{-- De vrais boutons radio, cachés derrière chaque dessin : on les choisit au doigt, à la souris ou au clavier. --}}
            <fieldset class="dg-avatars">
                <legend class="dg-visuellement-cache">Choisis ton avatar</legend>

                <label class="dg-choix-avatar dg-choix-avatar--initiales">
                    <input class="dg-choix-avatar__input" id="champ-avatar" type="radio" name="avatar" value="" @checked($current === '')>
                    <span class="dg-choix-avatar__image" aria-hidden="true">{{ $user->initials() }}</span>
                    <span class="dg-choix-avatar__nom">Mes initiales</span>
                </label>

                <div class="dg-avatars__grille">
                    @foreach ($avatars as $avatar => $name)
                        <label class="dg-choix-avatar">
                            <input class="dg-choix-avatar__input" type="radio" name="avatar" value="{{ $avatar }}" @checked($current === $avatar)>
                            <img class="dg-choix-avatar__image" src="{{ asset('avatars/'.$avatar.'.svg') }}" alt="" width="96" height="96">
                            <span class="dg-choix-avatar__nom">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- Toujours à portée de pouce, juste au-dessus des onglets, même en bas de la grille. --}}
            <div class="dg-barre-action">
                <button class="dg-bouton dg-bouton--large" type="submit" data-submit data-sending-label="Enregistrement…">
                    <span class="dg-bouton__texte">Enregistrer mon avatar</span>
                </button>
            </div>
        </form>
    </x-app-page>
@endsection
