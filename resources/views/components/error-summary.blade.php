{{--
    Récapitulatif des erreurs en haut du formulaire.
    Chaque message est un lien qui amène directement au champ à corriger.
--}}
@if ($errors->any())
    @php($fields = $errors->keys())
    <x-alert
        type="erreur"
        :title="count($fields) > 1 ? count($fields).' points à corriger' : 'Un point à corriger'"
        tabindex="-1"
        data-error-summary
    >
        <ul class="dg-alerte__liste">
            @foreach ($fields as $field)
                <li><a href="#champ-{{ str_replace('_', '-', $field) }}">{{ $errors->first($field) }}</a></li>
            @endforeach
        </ul>
    </x-alert>
@endif
