@php
    $empty = $empty ?? false;
    $compact = $compact ?? false;
    $item = $item ?? [];
    $path = $path ?? ($item['path'] ?? '');
    $displayName = trim(($item['code'] ?? '').(! empty($item['name']) ? ' - '.$item['name'] : ''));
@endphp

<div class="public-pedigree-node {{ $empty ? 'is-empty' : '' }} {{ $compact ? 'is-compact' : '' }}" data-path="{{ $path }}">
    <div class="public-pedigree-avatar">
        @if (! $empty && ! empty($item['photo_url']))
            <img src="{{ $item['photo_url'] }}" alt="{{ $item['name'] ?? $label ?? 'Familiar' }}">
        @else
            <i class="fas {{ $empty ? 'fa-minus' : 'fa-cow' }}" aria-hidden="true"></i>
        @endif
    </div>
    <div class="public-pedigree-info">
        <small>{{ $label ?? ($item['label'] ?? 'Familiar') }}</small>
        <strong>{{ $empty ? 'No registrado' : ($displayName ?: 'No registrado') }}</strong>
        <span>Raza: {{ $empty ? 'No registrada' : ($item['breed'] ?? 'No registrada') }}</span>
    </div>
</div>
