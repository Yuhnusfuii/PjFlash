import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js');
  });
}


// Dark mode toggle (ví dụ)
document.addEventListener('DOMContentLoaded', () => {
  // áp theme đã lưu
  if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark');
  }
  // nút toggle
  const toggle = document.querySelector('[data-theme-toggle]');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const root = document.documentElement;
      const isDark = root.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  }

  // Hiệu ứng fade-in đơn giản khi vào trang
  document.querySelectorAll('.fade-in').forEach(el => {
    el.classList.add('opacity-0', 'translate-y-2', 'transition', 'duration-500');
    requestAnimationFrame(() => el.classList.remove('opacity-0', 'translate-y-2'));
  });
});
