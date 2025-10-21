@props(['as' => 'div', 'pad' => true])

<{{ $as }} {{ $attributes->class([
  'y-card',
  'y-card-pad' => $pad,
]) }}>
  {{ $slot }}
</{{ $as }}>
