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

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <!-- Tailwind (DEV) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <!-- Livewire styles -->
    @livewireStyles

    <!-- Style tiện ích -->
    <style>
        .container-app { max-width: 80rem; margin: auto; padding: 0 1rem }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:.5rem 1rem; border-radius:20px; background:#10B981; color:#fff }
        .btn-outline { display:inline-flex; align-items:center; justify-content:center; padding:.5rem 1rem; border-radius:20px; border:1px solid #cbd5e1 }
        .dark .card { background:#1e293b; border-color:#334155 }
    </style>

    @stack('head')
</head>

<body class="min-h-screen antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    @include('layouts.navigation')

    <div class="flex">
        @include('layouts.sidebar')

        <main class="flex-1 min-h-[calc(100vh-4rem)] pb-16 md:pb-0">
            <div class="py-6 container-app view-fade" id="view">
                {{ $slot }}
            </div>
        </main>
    </div>

    @include('layouts.bottom-tab')

    <!-- Livewire scripts (bundle Alpine) -->
    @livewireScripts

    <!-- JS: theme + mobile menu -->
    <script>
        (function () {
            const root = document.documentElement;

            // Theme
            if (localStorage.getItem('theme') === 'dark') root.classList.add('dark');

            // Delegation
            document.addEventListener('click', (e) => {
                const tgl = e.target.closest('[data-theme-toggle]');
                if (tgl) {
                    const isDark = root.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    return;
                }
                const btn = e.target.closest('[data-mobile-toggle]');
                if (btn) {
                    const nav = btn.closest('nav');
                    const panel = nav?.nextElementSibling;
                    if (panel && panel.hasAttribute('data-mobile-panel')) {
                        const willOpen = panel.classList.contains('hidden');
                        panel.classList.toggle('hidden', !willOpen);
                        btn.setAttribute('aria-expanded', String(willOpen));
                    }
                }
            });

            // Hiệu ứng vào trang
            const view = document.getElementById('view');
            if (view) {
                view.style.opacity = 0; view.style.transform = 'translateY(8px)';
                view.style.transition = 'opacity .2s ease, transform .2s ease';
                requestAnimationFrame(() => { view.style.opacity = 1; view.style.transform = 'none'; });
            }
        })();
    </script>

    {{-- ĐÃ GỠ matching.js và bản @livewireScripts trùng --}}
    @stack('scripts')
</body>

</html>
