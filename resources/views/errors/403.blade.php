@extends('layouts.app')

@section('title', 'Akses Ditolak')
@section('page-title', 'Akses Ditolak')

@section('content')
<div class="empty-state">
    <div style="font-size: 64px; margin-bottom: 16px;">🚫</div>
    <h5 style="font-size: 20px; color: #DC3545;">403 — Akses Ditolak</h5>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <p class="text-muted fs-13">Role Anda: <strong>{{ ucfirst(session('user.role', '-')) }}</strong></p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
    </a>
</div>
@endsection