@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 py-10">

    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-4">Cài đặt tài khoản</h1>

    {{-- Flash message --}}
    @if (session('status'))
        <div class="y-card y-card-pad text-emerald-800 bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- =====================================================
         ================ ẢNH ĐẠI DIỆN ======================
         ===================================================== --}}
    @php
        /** URL hiện tại (nếu có) để fallback khi reset/huỷ chọn */
        $avatarUrl = $user->avatar_url;
    @endphp

    <div class="y-card">
        <div class="y-card-pad space-y-4">
            <h3 class="font-semibold text-lg">Ảnh đại diện</h3>

            <div class="flex items-start gap-6">

                {{-- Preview khung tròn --}}
                <div class="shrink-0">
                    <div class="h-20 w-20 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                        <img id="avatarPreview"
                             src="{{ $avatarUrl ?: 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><rect width=%2264%22 height=%2264%22 fill=%22%23f1f5f9%22/><text x=%2232%22 y=%2236%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2210%22 fill=%22%2394a3b8%22>no avatar</text></svg>' }}"
                             alt="avatar"
                             class="object-cover w-full h-full">
                    </div>
                </div>

                {{-- Form upload --}}
                <form method="POST"
                      action="{{ route('profile.avatar') }}"
                      enctype="multipart/form-data"
                      class="flex-1 space-y-3">
                    @csrf

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <input id="avatarInput"
                               type="file"
                               name="avatar"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="y-input y-input--soft max-w-xs">

                        <div class="flex items-center gap-2">
                            <button id="avatarResetBtn"
                                    type="button"
                                    class="y-btn"
                                    title="Huỷ chọn"
                                    aria-label="Huỷ chọn"
                                    hidden>
                                Reset
                            </button>

                            <button id="avatarSubmitBtn"
                                    class="y-btn y-btn--brand"
                                    disabled>
                                Upload
                            </button>
                        </div>
                    </div>

                    {{-- Info file được chọn --}}
                    <div id="avatarMeta" class="y-help"></div>

                    @error('avatar')
                        <div class="text-sm text-rose-600">{{ $message }}</div>
                    @enderror

                    {{-- Hint --}}
                    <p class="y-help">
                        Chỉ nhận ảnh <code>jpg, jpeg, png, webp</code>; tối đa <strong>2MB</strong>.
                    </p>

                    {{-- mang theo URL cũ để script reset dùng --}}
                    <input type="hidden" id="avatarOriginalUrl" value="{{ $avatarUrl }}">
                </form>
            </div>
        </div>
    </div>

    {{-- =====================================================
         ================ THÔNG TIN CÁ NHÂN =================
         ===================================================== --}}
    <div class="y-card">
        <div class="y-card-pad space-y-4">
            <h3 class="font-semibold text-lg">Thông tin cá nhân</h3>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="y-label">Tên hiển thị</label>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $user->name) }}"
                           class="y-input y-input--soft"
                           placeholder="Nhập tên hiển thị…">
                    @error('name') <div class="y-help text-rose-600">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="email" class="y-label">Email</label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $user->email) }}"
                           class="y-input y-input--soft"
                           readonly>
                    <p class="y-help">Email cố định. Liên hệ quản trị nếu cần thay đổi.</p>
                </div>

                <div class="pt-2">
                    <button class="y-btn y-btn--brand">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- =====================================================
         ================ ĐỔI MẬT KHẨU ======================
         ===================================================== --}}
    <div class="y-card">
        <div class="y-card-pad space-y-4">
            <h3 class="font-semibold text-lg">Đổi mật khẩu</h3>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="y-label">Mật khẩu hiện tại</label>
                    <input name="current_password" type="password"
                           class="y-input y-input--soft"
                           placeholder="Nhập mật khẩu hiện tại">
                    @error('current_password') <div class="y-help text-rose-600">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="y-label">Mật khẩu mới</label>
                    <input name="password" type="password"
                           class="y-input y-input--soft"
                           placeholder="Ít nhất 8 ký tự">
                    @error('password') <div class="y-help text-rose-600">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="y-label">Xác nhận mật khẩu mới</label>
                    <input name="password_confirmation" type="password"
                           class="y-input y-input--soft"
                           placeholder="Nhập lại mật khẩu mới">
                </div>

                <div class="pt-2">
                    <button class="y-btn y-btn--brand">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
(function () {
    const input   = document.getElementById('avatarInput');
    const img     = document.getElementById('avatarPreview');
    const reset   = document.getElementById('avatarResetBtn');
    const submit  = document.getElementById('avatarSubmitBtn');
    const meta    = document.getElementById('avatarMeta');
    const origUrl = document.getElementById('avatarOriginalUrl')?.value || '';

    function bytesToText(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
        return (bytes/1024/1024).toFixed(2) + ' MB';
    }

    function resetPreview() {
        if (origUrl) {
            img.src = origUrl;
        } else {
            img.src = 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><rect width=%2264%22 height=%2264%22 fill=%22%23f1f5f9%22/><text x=%2232%22 y=%2236%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2210%22 fill=%22%2394a3b8%22>no avatar</text></svg>';
        }
        meta.textContent = '';
        reset.hidden = true;
        submit.disabled = true;
    }

    input?.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) { resetPreview(); return; }

        if (!file.type.startsWith('image/')) {
            alert('Tệp không phải hình ảnh.');
            input.value = '';
            resetPreview();
            return;
        }
        const maxBytes = 2 * 1024 * 1024; // 2MB
        if (file.size > maxBytes) {
            alert('Tệp vượt quá 2MB.');
            input.value = '';
            resetPreview();
            return;
        }

        const url = URL.createObjectURL(file);
        img.src = url;
        meta.textContent = `${file.name} • ${file.type || 'image'} • ${bytesToText(file.size)}`;
        reset.hidden = false;
        submit.disabled = false;

        img.onload = () => { URL.revokeObjectURL(url); };
    });

    reset?.addEventListener('click', () => {
        input.value = '';
        resetPreview();
    });

    resetPreview();
})();
</script>
@endpush
