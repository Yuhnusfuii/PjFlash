@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 py-10">

    <h1 class="text-2xl font-bold text-slate-800 mb-4">Cài đặt tài khoản</h1>

    {{-- Flash message --}}
    @if (session('status'))
        <div class="p-3 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-md">
            {{ session('status') }}
        </div>
    @endif

    {{-- ===================================================== --}}
    {{-- ================ ẢNH ĐẠI DIỆN ======================== --}}
    {{-- ===================================================== --}}
    @php
        /** URL hiện tại (nếu có) để fallback khi reset/huỷ chọn */
        $avatarUrl = $user->avatar_url;
    @endphp

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold text-lg">Ảnh đại diện</h3>

        <div class="flex items-start gap-6">

            {{-- Preview khung tròn --}}
            <div class="shrink-0">
                <div class="h-20 w-20 rounded-full overflow-hidden border bg-slate-100">
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

                <div class="flex items-center gap-3">
                    <input id="avatarInput"
                        type="file"
                        name="avatar"
                        accept="image/*"
                        class="block w-full max-w-xs text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">

                    <button id="avatarResetBtn"
                        type="button"
                        class="btn-outline"
                        title="Huỷ chọn"
                        aria-label="Huỷ chọn"
                        hidden>
                        Reset
                    </button>

                    <button id="avatarSubmitBtn"
                        class="btn"
                        disabled>
                        Upload
                    </button>
                </div>

                {{-- Info file được chọn --}}
                <div id="avatarMeta" class="text-xs text-slate-500"></div>

                @error('avatar')
                    <div class="text-sm text-rose-600">{{ $message }}</div>
                @enderror

                {{-- Hint --}}
                <p class="text-xs text-slate-500">
                    Chỉ nhận ảnh <code>jpg, jpeg, png, webp</code>; tối đa 2MB.
                </p>

                {{-- mang theo URL cũ để script reset dùng --}}
                <input type="hidden" id="avatarOriginalUrl" value="{{ $avatarUrl }}">
            </form>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- ================ THÔNG TIN CÁ NHÂN =================== --}}
    {{-- ===================================================== --}}
    <div class="card p-6 space-y-4">
        <h3 class="font-semibold text-lg">Thông tin cá nhân</h3>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-600">Tên hiển thị</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                @error('name') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-600">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50" readonly>
            </div>

            <div class="pt-2">
                <button class="btn">Lưu thay đổi</button>
            </div>
        </form>
    </div>

    {{-- ===================================================== --}}
    {{-- ================ ĐỔI MẬT KHẨU ========================= --}}
    {{-- ===================================================== --}}
    <div class="card p-6 space-y-4">
        <h3 class="font-semibold text-lg">Đổi mật khẩu</h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-slate-600">Mật khẩu hiện tại</label>
                <input name="current_password" type="password"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                @error('current_password') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600">Mật khẩu mới</label>
                <input name="password" type="password"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                @error('password') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600">Xác nhận mật khẩu mới</label>
                <input name="password_confirmation" type="password"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="pt-2">
                <button class="btn">Cập nhật mật khẩu</button>
            </div>
        </form>
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
