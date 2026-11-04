@extends('layouts.recital')

@section('title', 'Confirme ton adresse')

@section('content')
    <x-piano-scene title="Vérifie ta boîte mail">
        <p class="dg-texte-doux">
            Un lien vient de partir à <strong class="dg-adresse">{{ auth()->user()->email }}</strong>.
            Ouvre-le pour activer ton compte&nbsp;: il est valable 60&nbsp;minutes.
            Pense à regarder dans les courriers indésirables.
        </p>

        @if (session('status'))
            <x-alert :title="session('status')" />
        @endif

        <form class="dg-formulaire" method="POST" action="{{ route('verification.send') }}" data-form>
            @csrf
            <button class="dg-bouton dg-bouton--large dg-bouton--secondaire" type="submit" data-submit data-sending-label="Envoi…">
                <span class="dg-bouton__texte">Renvoyer l’e-mail</span>
            </button>
        </form>

        <form class="dg-bascule" method="POST" action="{{ route('logout') }}">
            @csrf
            Ce n’est pas la bonne adresse&nbsp;?
            <button class="dg-lien dg-lien--fort dg-lien--bouton" type="submit">Se déconnecter</button>
        </form>
    </x-piano-scene>
@endsection
