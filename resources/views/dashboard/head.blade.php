@extends('layouts.app')

@section('title', 'Dashboard Kepala Toko')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan penggajian dan karyawan')

@section('content')

{{-- Periode Aktif --}}
@if(!empty($data['active_period']))
<div class="alert-custom alert-info mb-4">
    <i class="bi bi-calendar3-fill"></i>
    <span>Periode Aktif: <strong>{{ $data['active_period']['period_name'] }}</strong></span>
</div>
@else
<div class="alert-custom alert-warning mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>Tidak ada periode penggajian yang aktif.</span>
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $data['summary']['total_employees'] ?? 0 }}</div>
                <div class="stat-label">Total Karyawan</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $data['summary']['total_slips'] ?? 0 }}</div>
                <div class="stat-label">Total Slip</div>
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
            <div class="stat-icon red">
                <i class="bi bi-file-earmark-diff-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $data['summary']['draft_slips'] ?? 0 }}</div>
                <div class="stat-label">Slip Draft</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Karyawan per Kategori --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-tags-fill"></i>
                    Karyawan per Kategori
                </div>
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-primary fw-600">
                    <i class="bi bi-people"></i>
                </a>
            </div>
            <div class="card-body">
                @if(!empty($data['employee_by_category']))
                @foreach($data['employee_by_category'] as $cat)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-600 fs-14">{{ $cat['category_name'] }}</span>
                    <span class="badge-custom badge-open">
                        {{ $cat['employee_count'] }} orang
                    </span>
                </div>
                @if(!$loop->last)
                <hr style="margin:8px 0;border-color:#F0F0F0;">
                @endif
                @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">👥</div>
                    <p>Belum ada data karyawan.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Slip Gaji Terbaru --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    Slip Gaji Terbaru
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
                                <th>Kategori</th>
                                <th>Total Gaji</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['recent_slips'] as $slip)
                            <tr>
                                <td class="fw-600">{{ $slip['employee'] }}</td>
                                <td>{{ $slip['category'] }}</td>
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
                    <p>Belum ada slip gaji di periode ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection