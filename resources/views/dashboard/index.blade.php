@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di Sistem Remunerasi Restoran')

@section('content')
<div class="empty-state">
    <div class="empty-state-icon">👋</div>
    <h5>Halo, {{ session('user.name') }}!</h5>
    <p>Dashboard lengkap akan dibuat di Sesi 3. Sistem berjalan normal.</p>
    <span class="badge-custom badge-{{ session('user.role') }} mt-2">
        Role: {{ ucfirst(session('user.role')) }}
    </span>
</div>
@endsection