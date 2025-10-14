@props([
    'label' => '',
    'value' => 0,
])

<div {{ $attributes->class('card p-6') }}>
    <div class="text-sm text-slate-500">{{ $label }}</div>
    <div class="text-3xl font-semibold">{{ $value }}</div>
</div>
