{{--
    Mise en page commune des pages d'erreur (Laravel choisit errors/404.blade.php, errors/419.blade.php…).
    Rien ici ne dépend de la base de données ni de la session : la page s'affiche même si elles sont en panne.
--}}
@extends('layouts.recital')


@section('content')
    <x-piano-scene :title="$__env->yieldContent('title')">
        <p class="dg-texte-doux">@yield('message')</p>
        <a class="dg-bouton dg-bouton--large" href="@yield('link', route('home'))">
            <span class="dg-bouton__texte">@yield('action', 'Revenir à l’accueil')</span>
        </a>
        <p class="dg-code-erreur">Erreur @yield('code')</p>
    </x-piano-scene>
@endsection
