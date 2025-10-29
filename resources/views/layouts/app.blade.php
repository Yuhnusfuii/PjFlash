<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Yuhstud') }}</title>

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
    :root{
      --topbar-h: 4rem;
      --sidebar-w: 15rem;

      /* Tone Yuhstud */
      --brand: #10B981;
      --brand-600: #059669;

      /* Card + Surface */
      --card-bg: #fff;
      --card-br: #e2e8f0;

      /* Text */
      --text: #0f172a;
      --text-dim: #475569;
    }
    .dark:root{
      --card-bg: #1e293b;
      --card-br: #334155;
      --text: #e2e8f0;
      --text-dim: #94a3b8;
    }

    /* Helpers */
    .container-app { max-width: 80rem; margin: auto; padding: 0 1rem }
    .view-fade { transition: opacity .2s ease, transform .2s ease }

    /* Components (y-*) */
    .y-card{
      background: var(--card-bg);
      border: 1px solid var(--card-br);
      border-radius: 16px;
    }
    .y-card-pad{ padding: 1rem; }
    @media (min-width:768px){ .y-card-pad{ padding: 1.25rem; } }

    .y-btn{
      display:inline-flex; align-items:center; justify-content:center;
      gap:.5rem; padding:.6rem 1rem; border-radius: 9999px;
      font-weight: 600; transition: all .15s ease;
      border: 1px solid transparent;
    }
    .y-btn--brand{ background: var(--brand); color:#fff; }
    .y-btn--brand:hover{ background: var(--brand-600); }

    .y-input, .y-select{
      width:100%; border-radius: 12px; border:1px solid var(--card-br);
      background: var(--card-bg); color:var(--text);
      padding:.6rem .85rem; outline: none;
    }
    .y-input:focus, .y-select:focus{ box-shadow: 0 0 0 3px rgba(16,185,129,.2) }

    .y-label{ display:block; font-size:.875rem; color:var(--text-dim); }

    /* Theme btn */
    .theme-btn { height: 2.25rem; width: 2.25rem; border-radius: 9999px; display:flex; align-items:center; justify-content:center; border:1px solid var(--card-br) }
    .icon-sun{display:inline} .icon-moon{display:none}
    .dark .icon-sun{display:none} .dark .icon-moon{display:inline}

    /* Mobile panel slide */
    @keyframes slideIn { from { transform: translateX(-100%);} to { transform:none; } }
    .animate-slideIn{ animation: slideIn .2s ease; }
    @media (min-width:768px){ #__mb_ov{ display:none !important } }

    /* === Legacy compatibility: map class cũ -> style mới (giữ nguyên hành vi cũ) === */

    /* Buttons */
    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      gap:.5rem; padding:.6rem 1rem; border-radius:9999px;
      font-weight:600; transition:all .15s ease;
      background:var(--card-bg); color:var(--text); border:1px solid var(--card-br);
    }
    .btn:hover{ filter:brightness(0.98) }
    .btn:disabled{ opacity:.6; cursor:not-allowed }

    .btn-primary, .btn-success, .y-btn--brand{
      background:var(--brand); color:#fff; border-color:transparent;
    }
    .btn-primary:hover, .btn-success:hover, .y-btn--brand:hover{
      background:var(--brand-600);
    }

    .btn-outline{
      background:var(--card-bg); color:var(--text);
      border:1px solid var(--card-br);
    }
    .btn-danger{
      background:#ef4444; color:#fff; border-color:transparent;
    }
    .btn-danger:hover{ filter:brightness(0.95) }

    /* Inputs / Selects */
    .input, .select{
      width:100%; border-radius:12px; border:1px solid var(--card-br);
      background:var(--card-bg); color:var(--text);
      padding:.6rem .85rem; outline:none;
    }
    .input:focus, .select:focus{ box-shadow:0 0 0 3px rgba(16,185,129,.2) }

    /* Cards */
    .card, .ui-card{
      background:var(--card-bg);
      border:1px solid var(--card-br);
      border-radius:16px;
    }
    .card-body{ padding:1rem }
    @media(min-width:768px){ .card-body{ padding:1.25rem } }

    /* Chips/Badges (legacy) */
    .badge{
      display:inline-flex; align-items:center; height:1.5rem;
      padding:0 .6rem; border-radius:9999px; font-size:.75rem;
      border:1px solid var(--card-br); background:var(--card-bg); color:var(--text-dim);
    }

    /* Radios/checkbox */
    input[type="radio"], input[type="checkbox"]{
      accent-color: var(--brand);
    }

    /* Link button look (legacy) */
    .link-btn{ color:#64748b }
    .link-btn:hover{ text-decoration:underline }
  </style>

  @stack('head')
</head>
<body class="min-h-screen antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">

  {{-- Topbar cố định --}}
  @include('layouts.navigation')

  {{-- Sidebar cố định --}}
  @include('layouts.sidebar')

  {{-- Nội dung chính (chừa chỗ topbar + sidebar) --}}
  <main class="min-h-screen pt-[var(--topbar-h)] pl-[var(--sidebar-w)]">
    <div id="view" class="py-6 container-app view-fade">
      {{ $slot ?? '' }}
      @yield('content')
    </div>
  </main>

  {{-- Bottom tab (mobile) --}}
  @include('layouts.bottom-tab')

  @livewireScripts

  <script>
    (function(){
      const root = document.documentElement;
      if(localStorage.getItem('theme') === 'dark') root.classList.add('dark');

      // Mobile panel
      const panel = document.querySelector('[data-mobile-panel]');
      const overlayId = '__mb_ov';
      let overlay = document.getElementById(overlayId);

      function ensureOverlay(){
        if(overlay) return;
        overlay = document.createElement('div');
        overlay.id = overlayId;
        overlay.className = 'fixed inset-0 z-20 bg-black/40 md:hidden hidden';
        overlay.addEventListener('click', closePanel);
        document.body.appendChild(overlay);
      }
      function openPanel(){
        if(!panel) return;
        ensureOverlay();
        panel.classList.remove('hidden');
        panel.classList.add('animate-slideIn');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      function closePanel(){
        if(!panel) return;
        panel.classList.add('hidden');
        overlay && overlay.classList.add('hidden');
        document.body.style.overflow = '';
        document.querySelectorAll('[data-mobile-toggle]')
          .forEach(btn => btn.setAttribute('aria-expanded','false'));
      }

      document.addEventListener('click', (e)=>{
        const tgl = e.target.closest('[data-theme-toggle]');
        if(tgl){
          const dark = root.classList.toggle('dark');
          localStorage.setItem('theme', dark ? 'dark' : 'light');
          return;
        }
        const burger = e.target.closest('[data-mobile-toggle]');
        if(burger){
          const expanded = burger.getAttribute('aria-expanded') === 'true';
          if(expanded) closePanel();
          else { openPanel(); burger.setAttribute('aria-expanded','true'); }
        }
      });

      document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closePanel(); });

      // View fade-in
      const view = document.getElementById('view');
      if(view){ view.style.opacity=0; view.style.transform='translateY(8px)';
        requestAnimationFrame(()=>{ view.style.opacity=1; view.style.transform='none'; });
      }

      // Force-hide overlay on load to tránh chặn click khi mới vào trang
      document.addEventListener('DOMContentLoaded', ()=> {
        const ov = document.getElementById(overlayId);
        if (ov) ov.classList.add('hidden');
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>
