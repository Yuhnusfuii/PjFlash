// resources/js/app.js
import './bootstrap'
import 'alpinejs'

// Nếu có file riêng cho MCQ quiz và nó TỒN TẠI, giữ lại:
// import './mcq'

// Cho phép Vite copy assets tĩnh (nếu dùng)
import.meta.glob(['../images/**', '../fonts/**'], { eager: true })
