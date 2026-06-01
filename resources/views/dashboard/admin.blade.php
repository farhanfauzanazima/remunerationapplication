@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas penggajian Anda')

@section('content')

{{-- Periode Aktif --}}
@if(!empty($data['active_period']))
<div class="alert-custom alert-info mb-4">
    <i class="bi bi-calendar3-fill"></i>
    <span>Periode Aktif: <strong>{{ $data['active_period']['period_name'] }}</strong>
    ({{ \Carbon\Carbon::parse($data['active_period']['start_date'])->format('d M Y') }}
    s/d
    {{ \Carbon\Carbon::parse($data['active_period']['end_date'])->format('d M Y') }})</span>
</div>
@else
<div class="alert-custom alert-warning mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>Tidak ada periode penggajian yang aktif. Hubungi Kepala Toko.</span>
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">
                    {{ $data['summary']['my_slips_this_period'] ?? 0 }}
                </div>
                <div class="stat-label">Slip Dibuat</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $data['summary']['sent_slips'] ?? 0 }}</div>
                <div class="stat-label">Slip Terkirim</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-file-earmark-diff-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $data['summary']['draft_slips'] ?? 0 }}</div>
                <div class="stat-label">Masih Draft</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon teal">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:16px;">
                    {{ rupiah($data['summary']['total_salary_processed'] ?? 0) }}
                </div>
                <div class="stat-label">Total Diproses</div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-lightning-fill"></i>
                    Aksi Cepat
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('salary-slips.index') }}" class="btn btn-primary fw-600">
                        <i class="bi bi-plus-circle me-2"></i>Buat Slip Gaji
                    </a>
                    <a href="{{ route('emails.index') }}" class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-envelope me-2"></i>Kirim Email
                    </a>
                    <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-file-pdf me-2"></i>Generate PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Slip Terbaru --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-clock-history"></i>
            Slip Gaji Terbaru yang Saya Buat
        </div>
        <a href="{{ route('salary-slips.index') }}" class="btn btn-sm btn-primary fw-600">
            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        @if(!empty($data['recent_slips']))
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['recent_slips'] as $slip)
                    <tr>
                        <td class="fw-600">{{ $slip['employee'] }}</td>
                        <td>{{ $slip['period'] }}</td>
                        <td class="fw-600 text-success">
                            {{ rupiah($slip['total_salary']) }}
                        </td>
                        <td>{!! statusBadge($slip['status']) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📄</div>
            <h5>Belum Ada Slip Gaji</h5>
            <p>Mulai buat slip gaji untuk periode aktif.</p>
            <a href="{{ route('salary-slips.index') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-2"></i>Buat Slip Gaji
            </a>
        </div>
        @endif
    </div>
</div>

@endsection