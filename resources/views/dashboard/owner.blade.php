@extends('layouts.app')

@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem remunerasi restoran')

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
    <span>Tidak ada periode penggajian yang aktif saat ini.</span>
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
                <div class="stat-value">
                    {{ $data['summary']['total_active_employees'] ?? 0 }}
                </div>
                <div class="stat-label">Karyawan Aktif</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:16px;">
                    {{ rupiah($data['summary']['total_salary_this_period'] ?? 0) }}
                </div>
                <div class="stat-label">Total Gaji Periode Ini</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">
                    {{ $data['summary']['total_slips_this_period'] ?? 0 }}
                </div>
                <div class="stat-label">Total Slip Dibuat</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon teal">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">
                    {{ $data['summary']['sent_slips_this_period'] ?? 0 }}
                </div>
                <div class="stat-label">Slip Terkirim</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Tren Gaji --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-graph-up-arrow"></i>
                    Tren Biaya Gaji (6 Periode Terakhir)
                </div>
            </div>
            <div class="card-body">
                @if(!empty($data['salary_trend']))
                <div style="overflow-x:auto;">
                    <table class="table-custom w-100">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Total Karyawan</th>
                                <th>Total Gaji</th>
                                <th>Grafik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxSalary = collect($data['salary_trend'])->max('total_salary') ?: 1;
                            @endphp
                            @foreach($data['salary_trend'] as $trend)
                            @php
                                $pct = ($trend['total_salary'] / $maxSalary) * 100;
                            @endphp
                            <tr>
                                <td class="fw-600">{{ $trend['period_name'] }}</td>
                                <td>{{ $trend['total_slips'] }} orang</td>
                                <td class="fw-600 text-success">
                                    {{ rupiah($trend['total_salary']) }}
                                </td>
                                <td style="width:200px;">
                                    <div style="background:#F0F0F0;border-radius:4px;height:8px;overflow:hidden;">
                                        <div style="background:var(--primary);height:100%;width:{{ $pct }}%;border-radius:4px;transition:width 0.5s;"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <p>Belum ada data tren gaji.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Ringkasan per Kategori --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-tags-fill"></i>
                    Karyawan per Kategori
                </div>
            </div>
            <div class="card-body">
                @if(!empty($data['category_stats']))
                @foreach($data['category_stats'] as $cat)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-600 fs-14">{{ $cat['category_name'] }}</div>
                        <div class="fs-13 text-muted">{{ rupiah($cat['base_salary']) }}/bulan</div>
                    </div>
                    <div class="text-end">
                        <div class="stat-value" style="font-size:20px;">
                            {{ $cat['employee_count'] }}
                        </div>
                        <div class="fs-13 text-muted">orang</div>
                    </div>
                </div>
                @if(!$loop->last)
                <hr style="margin:8px 0;border-color:#F0F0F0;">
                @endif
                @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <p>Belum ada data kategori.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Status Email --}}
        @if(!empty($data['email_stats']))
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-envelope-fill"></i>
                    Status Email Periode Ini
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-14">Terkirim</span>
                    <span class="badge-custom badge-sent">
                        {{ $data['email_stats']['sent'] ?? 0 }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-14">Gagal</span>
                    <span class="badge-custom badge-inactive">
                        {{ $data['email_stats']['failed'] ?? 0 }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fs-14">Pending</span>
                    <span class="badge-custom badge-draft">
                        {{ $data['email_stats']['pending'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Periode Terbaru --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-calendar3"></i>
                    Periode Penggajian Terbaru
                </div>
                <a href="{{ route('periods.index') }}" class="btn btn-sm btn-primary fw-600">
                    <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if(!empty($data['recent_periods']))
                <div style="overflow-x:auto;">
                    <table class="table-custom w-100">
                        <thead>
                            <tr>
                                <th>Nama Periode</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Akhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['recent_periods'] as $period)
                            <tr>
                                <td class="fw-600">{{ $period['period_name'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($period['start_date'])->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($period['end_date'])->format('d M Y') }}</td>
                                <td>{!! statusBadge($period['status']) !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📅</div>
                    <p>Belum ada periode penggajian.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection