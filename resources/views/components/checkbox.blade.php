{{--
    Case à cocher. Le texte de la case peut contenir des liens.
    Utilisation : <x-checkbox name="terms" required>J'accepte les <a href="…">conditions</a>.</x-checkbox>
--}}
@props(['name'])

@php
    $id = 'champ-'.str_replace('_', '-', $name);
    $hasError = $errors->has($name);
@endphp

<div @class(['dg-case', 'dg-case--erreur' => $hasError])>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="checkbox"
        value="1"
        class="dg-case__input"
        @checked(old($name))
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-erreur" @endif
        {{ $attributes }}
    >
    <label class="dg-case__label" for="{{ $id }}">{{ $slot }}</label>
    @error($name)
        <p class="dg-champ__erreur" id="{{ $id }}-erreur">{{ $message }}</p>
    @enderror
</div>
