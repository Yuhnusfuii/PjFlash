@props(['label' => null, 'name' => null])

<div {{ $attributes->class('space-y-1') }}>
  @if($label)
    <label class="y-label" for="{{ $name }}">{{ $label }}</label>
  @endif

  <select id="{{ $name }}" name="{{ $name }}"
          {{ $attributes->merge(['class' => 'y-select']) }}>
    {{ $slot }}
  </select>

  @error($name)
    <p class="text-red-500 text-sm">{{ $message }}</p>
  @enderror
</div>
