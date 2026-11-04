{{--
    Message en haut de page.
    type="succes" (par défaut) : annoncé poliment. type="erreur" : annoncé tout de suite.
    Utilisation : <x-alert :title="session('status')" />
--}}
@props([
    'type' => 'succes',
    'title' => null,
])

<div {{ $attributes->merge([
    'class' => 'dg-alerte dg-alerte--'.$type,
    'role' => $type === 'erreur' ? 'alert' : 'status',
]) }}>
    @if ($type === 'erreur')
        <svg class="dg-alerte__icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5"/><path d="M12 16.5h.01"/></svg>
    @else
        <svg class="dg-alerte__icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.7 2.7L16 9.8"/></svg>
    @endif
    <div class="dg-alerte__corps">
        @if ($title)
            <p class="dg-alerte__titre">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
