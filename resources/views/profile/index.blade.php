@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Profil Saya</li>
            </ol>
        </nav>
        <h1>Profil Saya</h1>
        <p>Kelola informasi akun dan keamanan Anda</p>
    </div>
</div>

<div class="row g-4">

    {{-- Info Profil --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div style="width:80px;height:80px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#212529;margin:0 auto 16px;">
                    {{ strtoupper(substr(session('user.name', 'U'), 0, 1)) }}
                </div>
                <h5 class="fw-700 mb-1">{{ session('user.name') }}</h5>
                <p class="text-muted fs-14 mb-2">{{ session('user.email') }}</p>
                <span class="badge-custom badge-{{ session('user.role') }}">
                    {{ ucfirst(session('user.role')) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Form Update Profil --}}
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-person-fill"></i>
                    Informasi Profil
                </div>
            </div>
            <div class="card-body">

                @if(session('success'))
                <div class="alert-custom alert-success alert-auto-hide">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert-custom alert-error alert-auto-hide">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user['name'] ?? session('user.name')) }}"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user['email'] ?? session('user.email')) }}"
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone', $user['phone'] ?? session('user.phone')) }}"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ ucfirst(session('user.role')) }}"
                                   disabled>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary fw-600">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-shield-lock-fill"></i>
                    Ganti Password
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Password Lama</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Password saat ini"
                                   required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password"
                                   name="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   placeholder="Minimal 6 karakter"
                                   required>
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password"
                                   name="new_password_confirmation"
                                   class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi password baru"
                                   required>
                            @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger fw-600">
                            <i class="bi bi-key-fill me-2"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection