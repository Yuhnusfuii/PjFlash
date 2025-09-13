// resources/js/app.js
import './bootstrap';

// Ngăn khởi tạo trùng (nếu app.js bị nạp 2 lần ở đâu đó)
if (!window.__pjflashInit) {
  window.__pjflashInit = true;

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;

    // Khởi tạo theme theo localStorage (mặc định light)
    root.classList.toggle('dark', localStorage.getItem('theme') === 'dark');

    // Chống double-tap khi lỡ có listener khác
    let themeGuard = false;

    // Delegation: toggle dark + mở/đóng mobile panel
    document.addEventListener('click', (e) => {
      const t1 = e.target.closest('[data-theme-toggle]');
      if (t1) {
        if (themeGuard) return;
        themeGuard = true;
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        setTimeout(() => (themeGuard = false), 200);
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
      view.style.transition = 'opacity .2s cubic-bezier(0.2,0,0.2,1), transform .2s cubic-bezier(0.2,0,0.2,1)';
      requestAnimationFrame(() => { view.style.opacity = 1; view.style.transform = 'none'; });
    }
  });
}
