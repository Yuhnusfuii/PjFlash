{{-- resources/views/dashboard.blade.php --}}
@push('styles')
<style>
  /* CSS riêng cho trang này (tuỳ chọn) */
  .hero { background: radial-gradient(1200px 600px at 10% 0%, #e0f2fe 0, transparent 60%); }
</style>
@endpush

<x-app-layout>
  <div class="container-app py-6 view-fade">
    <div class="card p-6">
      You're logged in!
    </div>
  </div>
</x-app-layout>

@push('scripts')
<script>
  // JS riêng cho Dashboard (nếu cần)
</script>
@endpush
