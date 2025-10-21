<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name','Yuhstud') }}</title>

    <!-- Tailwind (CDN, same as app layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <style>
        :root { --card-radius: 22px; }
        .card {
            border-radius: var(--card-radius);
            border: 1px solid rgb(226 232 240);
            background: white;
        }
        .dark .card { background: rgb(30 41 59); border-color: rgb(51 65 85); }
        .brand-shadow { filter: drop-shadow(0 8px 24px rgba(2,132,199,.15)); }
        .btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  border-radius: 0.75rem;
  padding: 0.6rem 1.25rem;
  transition: all .2s ease;
}

.btn-primary {
  background-color: #0ea5e9;
  color: white;
  box-shadow: 0 4px 10px rgba(14,165,233,0.15);
}
.btn-primary:hover {
  background-color: #0284c7;
  box-shadow: 0 6px 14px rgba(14,165,233,0.25);
}

.btn-outline {
  border: 1px solid #cbd5e1;
  color: #0ea5e9;
  background-color: transparent;
}
.btn-outline:hover {
  background-color: rgba(14,165,233,0.08);
}

.dark .btn-outline {
  border-color: #475569;
  color: #7dd3fc;
}
.dark .btn-outline:hover {
  background-color: rgba(56,189,248,0.1);
}

a {
  color: #0284c7;
  transition: color .15s;
}
a:hover {
  color: #0ea5e9;
}

    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased">

    <!-- Theme toggle (top-right) -->
    <button data-theme-toggle
            class="theme-btn fixed right-4 top-4 z-10 bg-white/70 dark:bg-slate-800/70 backdrop-blur">
        <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/>
            <path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5M17.5 17.5 19 19M19 5l-1.5 1.5M5 19l1.5-1.5"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="flex flex-col items-center justify-center px-4 py-12 sm:py-20">
        <!-- Brand -->
        <div class="mb-8 text-center">
            <picture class="brand-shadow inline-block">
                <source srcset="{{ asset('icons/logo dark mode.png') }}" media="(prefers-color-scheme: dark)">
                <img src="{{ asset('icons/logo light mode.png') }}" alt="Yuhstud"
                     class="w-[180px] h-auto mx-auto select-none">
            </picture>
        </div>

        <!-- Card -->
        <div class="card w-full max-w-[460px] p-6 sm:p-8">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="mt-10 text-xs text-slate-500">
            © {{ date('Y') }} {{ config('app.name','Yuhstud') }}. All rights reserved.
        </div>
    </div>

    <script>
        (function () {
            const root = document.documentElement;
            if (localStorage.getItem('theme') === 'dark') root.classList.add('dark');
            document.addEventListener('click', (e) => {
                const t = e.target.closest('[data-theme-toggle]');
                if (!t) return;
                const isDark = root.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        })();
    </script>
</body>
</html>
