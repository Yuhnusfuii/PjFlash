@props(['variant' => 'brand', 'disabled' => false, 'type' => 'button'])

@php
  $map = [
    'brand'   => 'y-btn y-btn--brand',
    'ghost'   => 'y-btn border border-[var(--card-br)] bg-transparent text-[var(--text)] hover:bg-white/5',
    'outline' => 'y-btn bg-transparent border border-[var(--brand)] text-[var(--brand)] hover:bg-[var(--brand)] hover:text-white',
  ];
@endphp

<button type="{{ $type }}" @disabled($disabled)
  {{ $attributes->class($map[$variant] ?? $map['brand']) }}>
  {{ $slot }}
</button>
