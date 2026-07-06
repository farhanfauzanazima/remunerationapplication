@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan penggajian ' . (session('user.role') === 'owner' ? 'seluruh cabang' : 'cabang Anda'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Karyawan Aktif</div>
            <div class="fs-3 fw-bold">{{ $stats['total_karyawan_aktif'] ?? 0 }}</div>
            <div class="text-muted small">{{ $stats['total_karyawan_tetap'] ?? 0 }} Tetap · {{ $stats['total_karyawan_partime'] ?? 0 }} Partime</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Periode Terakhir</div>
            <div class="fs-5 fw-bold">{{ $stats['periode_terakhir']['name'] ?? '-' }}</div>
            <div class="text-muted small">{{ $stats['total_slip_periode_terakhir'] ?? 0 }} slip diproses</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Total Gaji Periode Terakhir</div>
            <div class="fs-5 fw-bold">Rp{{ number_format($stats['total_gaji_periode_terakhir'] ?? 0, 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Distribusi</div>
            <div class="fs-6">
                <span class="badge bg-success">{{ $stats['distribusi']['terkirim'] ?? 0 }} Terkirim</span>
                <span class="badge bg-danger">{{ $stats['distribusi']['gagal'] ?? 0 }} Gagal</span>
                <span class="badge bg-secondary">{{ $stats['distribusi']['pending'] ?? 0 }} Pending</span>
            </div>
        </div></div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('salary-slips.bulk-create') }}" class="btn btn-primary"><i class="bi bi-collection-fill"></i> Input Massal Slip Gaji</a>
    <a href="{{ route('distribution.index') }}" class="btn btn-outline-success"><i class="bi bi-send-fill"></i> Distribusi Gaji</a>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-bar-chart-fill"></i> Lihat Laporan</a>
</div>
@endsection