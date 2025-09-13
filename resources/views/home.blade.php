<x-guest-layout>
  <div class="container-app py-10">
    <div class="card p-6 view-fade">
      <h1 class="text-2xl font-semibold">PjFlash</h1>
      <p class="mt-2 text-slate-600">Trang giới thiệu công khai. Đăng nhập để vào Dashboard.</p>
      <div class="mt-4 flex gap-2">
        <a href="{{ route('login') }}" class="btn">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn-outline">Đăng ký</a>
      </div>
    </div>
  </div>
</x-guest-layout>
