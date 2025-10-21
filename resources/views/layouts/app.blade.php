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

    /* Card chuẩn */
    .ui-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; }
    .dark .ui-card { background:#0f172a; border-color:#334155; }

    /* “Tabs” bo tròn phía trên nhưng vẽ BÊN TRONG card => không chồng lên hàng khác */
    .stacked { position: relative; padding-top: 18px; isolation: isolate; }
    .stacked::before, .stacked::after{
      content:""; position:absolute; left:10px; right:10px; height:12px;
      border:1px solid #e2e8f0; border-bottom:none; border-radius:12px 12px 0 0;
      pointer-events:none; background: transparent;
    }
    .stacked::before{ top:4px; }
    .stacked::after { top:10px; opacity:.7; }
    .dark .stacked::before, .dark .stacked::after { border-color:#334155; }

    /* Nút tiện dụng (dùng @apply từ CDN chỉ để đọc – khi build hãy đưa vào CSS) */
    .btn-primary { @apply inline-flex items-center justify-center px-4 h-10 rounded-2xl bg-emerald-500 text-white hover:bg-emerald-600 transition; }
    .btn-secondary { @apply inline-flex items-center justify-center px-3 h-10 rounded-2xl border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition; }
    .btn-danger { @apply inline-flex items-center justify-center px-3 h-10 rounded-2xl bg-rose-500 text-white hover:bg-rose-600 transition; }
    .pill { @apply inline-flex items-center justify-center px-2.5 h-6 rounded-full text-xs border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900; }
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

      const view = document.getElementById('view');
      if(view){ view.style.opacity=0; view.style.transform='translateY(8px)';
        requestAnimationFrame(()=>{ view.style.opacity=1; view.style.transform='none'; });
      }
    })();
  </script>

  @stack('scripts')
</body>
</html>
