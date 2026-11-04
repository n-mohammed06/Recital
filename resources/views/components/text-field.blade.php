{{--
    Champ de texte au libellé « flottant » : le libellé est écrit dans le champ, puis il remonte
    quand on tape. C'est un vrai <label> : il reste visible et lu par les lecteurs d'écran.
    L'aide et le message d'erreur s'affichent sous le champ.

    Utilisation : <x-text-field name="email" label="Adresse e-mail" type="email" icon="mail" autocomplete="email" required />
    icon : « mail » ou « user » (facultatif). value : valeur de départ (facultatif).
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'hint' => null,
    'icon' => null,
    'value' => null,
])

@php
    $id = 'champ-'.str_replace('_', '-', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hint ? $id.'-aide' : null,
        $hasError ? $id.'-erreur' : null,
    ])->filter()->implode(' ');
@endphp

<div @class(['dg-champ', 'dg-champ--icone' => $icon, 'dg-champ--erreur' => $hasError])>
    <div class="dg-champ__boite">
        @if ($icon === 'mail')
            <svg class="dg-champ__icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7l8 6 8-6"/></svg>
        @elseif ($icon === 'user')
            <svg class="dg-champ__icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8.5" r="3.5"/><path d="M5 19.5c1.2-3.3 3.8-5 7-5s5.8 1.7 7 5"/></svg>
        @endif
        {{-- placeholder=" " : sert seulement à savoir si le champ est vide, pour placer le libellé. --}}
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder=" "
            class="dg-champ__input"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes }}
        >
        <label class="dg-champ__label" for="{{ $id }}">{{ $label }}</label>
    </div>
    @if ($hint)
        <p class="dg-champ__aide" id="{{ $id }}-aide">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="dg-champ__erreur" id="{{ $id }}-erreur">{{ $message }}</p>
    @enderror
</div>
