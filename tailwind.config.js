import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
// tailwind.config.js
export default {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: { sans: ['Roboto', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Inter'] },
      colors: {
        brand: { DEFAULT: '#10B981', 50:'#ECFDF5', 100:'#D1FAE5', 200:'#A7F3D0', 300:'#6EE7B7', 400:'#34D399', 500:'#10B981', 600:'#059669', 700:'#047857', 800:'#065F46', 900:'#064E3B' }
      },
      borderRadius: {
        '2xl': '20px', // yêu cầu 20px
      },
      transitionTimingFunction: {
        'ui': 'cubic-bezier(0.2, 0.0, 0.2, 1)', // ease-out mượt
      },
      transitionDuration: {
        'ui': '200ms',
      }
    },
  },
  plugins: [],
}


