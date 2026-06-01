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

        {{-- Flash Error --}}
        @if (session('error'))
            <div class="alert-custom alert-error mb-3">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Login --}}
        <form action="{{ route('auth.login.post') }}" method="POST" id="loginForm">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-envelope" style="color: #6c757d;"></i>
                    </span>
                    <input type="email" name="email"
                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                        placeholder="owner@resto.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger fs-13 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-lock" style="color: #6c757d;"></i>
                    </span>
                    <input type="password" name="password" id="passwordInput"
                        class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                        placeholder="••••••••" required>
                    <button class="input-group-text bg-light border-start-0" type="button" onclick="togglePassword()"
                        title="Lihat password">
                        <i class="bi bi-eye" id="eyeIcon" style="color: #6c757d;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger fs-13 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-600" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Masuk ke Sistem
            </button>

        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                &copy; {{ date('Y') }} Sistem Remunerasi Restoran
            </small>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
@endpush
