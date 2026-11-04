{{--
    Page publique pendant le mode « Bientôt disponible » (app/Http/Middleware/ComingSoon.php).
    Elle s'affiche avant les sessions : rien ici ne doit dépendre de la session ni de l'élève connecté.
--}}
@extends('layouts.recital')

@section('title', 'Bientôt disponible')
@section('theme-color', '#1C0806')

@section('content')
    <x-piano-scene :big="true">
        <section class="dg-bientot" aria-labelledby="titre-bientot">
            <p class="dg-pastille">Bientôt disponible</p>
            <h2 class="dg-titre-2" id="titre-bientot">Le rideau va bientôt se lever</h2>
            <p class="dg-bientot__texte">Récital t’apprendra le piano sur ton téléphone, quelques minutes par jour, même si tu n’as jamais touché un clavier.</p>
            <p class="dg-texte-doux">Le site est en préparation. Reviens bientôt pour jouer ta première note.</p>
        </section>

        <x-legal-links />
    </x-piano-scene>
@endsection
