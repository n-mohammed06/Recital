{{--
    Champ de mot de passe au libellé flottant, avec le bouton « œil » pour l'afficher ou le masquer.
    :rules="true" ajoute la liste des règles, cochées au fur et à mesure de la saisie.
    Utilisation : <x-password-field autocomplete="current-password" required />
--}}
@props([
    'name' => 'password',
    'label' => 'Mot de passe',
    'rules' => false,
])

@php
    $id = 'champ-'.str_replace('_', '-', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([
        $rules ? $id.'-regles' : null,
        $hasError ? $id.'-erreur' : null,
    ])->filter()->implode(' ');
@endphp

<div @class(['dg-champ', 'dg-champ--icone', 'dg-champ--erreur' => $hasError]) data-password-field>
    <div class="dg-champ__boite">
        <svg class="dg-champ__icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="5" y="10.5" width="14" height="10" rx="2.5"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="password"
            placeholder=" "
            class="dg-champ__input"
            autocapitalize="none"
            autocorrect="off"
            spellcheck="false"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes }}
        >
        <label class="dg-champ__label" for="{{ $id }}">{{ $label }}</label>
        {{-- Caché tant que le JavaScript n'est pas chargé : sans lui, le bouton ne servirait à rien. --}}
        <button type="button" class="dg-champ__oeil" data-toggle-password aria-controls="{{ $id }}" aria-label="Afficher le mot de passe" aria-pressed="false" hidden>
            <svg data-icon="show" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg data-icon="hide" viewBox="0 0 24 24" aria-hidden="true" focusable="false" hidden><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M4 4l16 16"/></svg>
        </button>
    </div>
    @if ($rules)
        {{-- Sans JavaScript, la liste reste une simple aide. Avec, chaque règle se coche pendant la saisie. --}}
        <ul class="dg-regles" id="{{ $id }}-regles" data-rules>
            <li data-rule="length">8 caractères minimum<span class="dg-visuellement-cache" data-rule-state></span></li>
            <li data-rule="letter">une lettre<span class="dg-visuellement-cache" data-rule-state></span></li>
            <li data-rule="digit">un chiffre<span class="dg-visuellement-cache" data-rule-state></span></li>
        </ul>
    @endif
    @error($name)
        <p class="dg-champ__erreur" id="{{ $id }}-erreur">{{ $message }}</p>
    @enderror
</div>
