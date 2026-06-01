@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card">

    {{-- Logo --}}
    <div class="auth-logo">
        <div class="auth-logo-icon">🍽️</div>
        <h1 class="auth-title">Sistem Remunerasi</h1>
        <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
    <div class="alert-custom alert-error mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert-custom alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Form Login --}}
    <form action="{{ route('auth.login.post') }}" method="POST" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">
                <i class="bi bi-envelope me-1"></i>Email
            </label>
            <input
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="email@restoran.com"
                value="{{ old('email') }}"
                required
                autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label class="form-label">
                <i class="bi bi-lock me-1"></i>Password
            </label>
            <div class="input-group">
                <input
                    type="password"
                    name="password"
                    id="passwordInput"
                    class="form-control border-end-0 @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    required>
                <button class="btn btn-outline-secondary border-start-0"
                        type="button"
                        id="togglePasswordBtn"
                        onclick="togglePassword()"
                        tabindex="-1">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
                class="btn btn-primary w-100 py-2 fw-600"
                id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            <span id="loginBtnText">Masuk ke Sistem</span>
        </button>

    </form>

    {{-- Info default akun --}}
    @if(config('app.debug'))
    <div class="mt-3 p-3 rounded" style="background:#F8F9FA; font-size:12px; color:#6c757d;">
        <div class="fw-600 mb-1">👤 Akun Default (Debug Mode):</div>
        <div>Owner: owner@resto.com / password123</div>
        <div>Head: head@resto.com / password123</div>
        <div>Admin: admin@resto.com / password123</div>
    </div>
    @endif

    <div class="text-center mt-3">
        <small class="text-muted">
            &copy; {{ date('Y') }} Sistem Remunerasi Restoran
        </small>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Toggle show/hide password
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');

    if (input.type === 'password') {
        input.type    = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type    = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Loading state saat form submit
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn     = document.getElementById('loginBtn');
    const btnText = document.getElementById('loginBtnText');

    btn.disabled    = true;
    btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});
</script>
@endpush