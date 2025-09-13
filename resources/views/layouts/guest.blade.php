<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

   <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <!-- Chỉ nạp JS qua Vite -->
    @vite(['resources/js/app.js'])

    <style>
    .container-app{max-width:80rem;margin:auto;padding:0 1rem}
    .card{background:#fff;border:1px solid #e2e8f0;border-radius:20px}
    .btn{display:inline-flex;align-items:center;justify-content:center;padding:.5rem 1rem;border-radius:20px;background:#10B981;color:#fff}
    .btn-outline{display:inline-flex;align-items:center;justify-content:center;padding:.5rem 1rem;border-radius:20px;border:1px solid #cbd5e1}
    .dark .card{background:#1e293b;border-color:#334155}
    </style>

  </head>

  <body class="min-h-screen antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
      <button data-theme-toggle class="absolute right-4 top-4 inline-flex items-center justify-center px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800" title="Toggle theme">🌙</button>

      <div>
        <a href="/">
          <x-application-logo class="w-20 h-20 fill-current text-brand" />
        </a>
      </div>

      <div class="w-full sm:max-w-md mt-6 px-6 py-4 card shadow-md overflow-hidden">
        {{ $slot }}
      </div>
    </div>

    @stack('scripts')
  </body>
</html>
