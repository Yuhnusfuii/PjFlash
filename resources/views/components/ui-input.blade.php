@props(['label' => null, 'name' => null, 'type' => 'text'])

<div {{ $attributes->class('space-y-1') }}>
  @if($label)
    <label class="y-label" for="{{ $name }}">{{ $label }}</label>
  @endif

  <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
         {{ $attributes->merge(['class' => 'y-input']) }}>

  @error($name)
    <p class="text-red-500 text-sm">{{ $message }}</p>
  @enderror
</div>
