@props(['type' => 'warn'])

@php
  $classes = match($type){
    'success' => 'text-emerald-700 bg-emerald-50 border-emerald-200 dark:text-emerald-300 dark:bg-emerald-900/30',
    'danger'  => 'text-red-700 bg-red-50 border-red-200 dark:text-red-300 dark:bg-red-900/30',
    default   => 'text-amber-700 bg-amber-50 border-amber-200 dark:text-amber-300 dark:bg-amber-900/30',
  };
@endphp

<div {{ $attributes->class("border rounded-xl px-3 py-2 text-sm $classes") }}>
  {{ $slot }}
</div>
