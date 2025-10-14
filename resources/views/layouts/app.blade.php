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

    @livewireStyles

    <style>
        .container-app { max-width: 80rem; margin: auto; padding: 0 1rem }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:.5rem 1rem; border-radius:20px; background:#10B981; color:#fff }
        .btn-outline { display:inline-flex; align-items:center; justify-content:center; padding:.5rem 1rem; border-radius:20px; border:1px solid #cbd5e1 }
        .dark .card { background:#1e293b; border-color:#334155 }
        /* Kích thước cố định */
        :root { --topbar-h: 4rem; --sidebar-w: 15rem; }
        /* Theme toggle icon */
        .theme-btn { height: 2.25rem; width: 2.25rem; border-radius: 9999px; display:flex; align-items:center; justify-content:center; border:1px solid #e2e8f0; }
        .dark .theme-btn { border-color:#334155; }
        .theme-icon { width: 18px; height: 18px; position: relative; }
        .theme-icon .sun, .theme-icon .moon { position:absolute; inset:0; transition: all .25s ease; }
        .theme-icon .moon { opacity:0; transform: scale(.6) rotate(-20deg); }
        .dark .theme-icon .sun { opacity:0; transform: scale(.6) rotate(20deg); }
        .dark .theme-icon .moon { opacity:1; transform: scale(1) rotate(0); }

    </style>

    @stack('head')
</head>

<body class="min-h-screen antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    {{-- Topbar fixed --}}
    @include('layouts.navigation')

    {{-- Sidebar fixed --}}
    @include('layouts.sidebar')

    {{-- Main content: chừa chỗ cho topbar + sidebar --}}
    <main class="min-h-screen pt-[var(--topbar-h)] pl-[var(--sidebar-w)]">
        <div class="py-6 container-app view-fade" id="view">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    {{-- Bottom-tab nếu có (giữ nguyên) --}}
    @include('layouts.bottom-tab')

    @livewireScripts

<script>
  (function () {
      const root = document.documentElement;

      // Set theme theo localStorage (mặc định light)
      const stored = localStorage.getItem('theme');
      if (stored === 'dark') root.classList.add('dark');
      if (stored === 'light') root.classList.remove('dark');

      // CLICK HANDLER cho nút [data-theme-toggle]
      document.addEventListener('click', (e) => {
          const tgl = e.target.closest('[data-theme-toggle]');
          if (!tgl) return;

          const isDark = root.classList.toggle('dark');
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
          tgl.setAttribute('aria-pressed', isDark ? 'true' : 'false');
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


    @stack('scripts')
</body>
</html>
