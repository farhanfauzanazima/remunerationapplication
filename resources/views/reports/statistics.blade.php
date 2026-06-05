@extends('layouts.app')

@section('title', 'Statistik')
@section('page-title', 'Statistik & Tren')
@section('page-subtitle', 'Analisis tren biaya gaji')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('reports.index') }}">Laporan</a>
                </li>
                <li class="breadcrumb-item active">Statistik</li>
            </ol>
        </nav>
        <h1>Statistik & Tren</h1>
        <p>Analisis tren biaya gaji dan distribusi karyawan</p>
    </div>
</div>

@php
    $trend    = $data['salary_trend'] ?? [];
    $catDist  = $data['category_distribution'] ?? [];
    $maxSalary = collect($trend)->max('total_salary') ?: 1;
    $maxCount  = collect($catDist)->max('total_employee') ?: 1;
@endphp

{{-- Tren Gaji --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-graph-up-arrow"></i>
            Tren Biaya Gaji ({{ count($trend) }} Periode Terakhir)
        </div>
    </div>
    <div class="card-body">
        @if(count($trend) > 0)

        {{-- Bar Chart Sederhana --}}
        <div class="mb-4" style="overflow-x:auto;">
            <div style="display:flex;align-items:flex-end;gap:12px;height:200px;padding:0 8px;">
                @foreach($trend as $t)
                @php
                    $barHeight = ($t['total_salary'] / $maxSalary) * 180;
                @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;min-width:60px;">
                    <div class="fw-700 fs-13 text-success" style="font-size:11px!important;white-space:nowrap;">
                        {{ rupiah($t['total_salary']) }}
                    </div>
                    <div style="width:100%;height:{{ $barHeight }}px;background:var(--primary);border-radius:4px 4px 0 0;transition:all 0.3s;position:relative;"
                         title="{{ $t['period_name'] }}: {{ rupiah($t['total_salary']) }}">
                    </div>
                    <div class="fs-13 text-muted text-center" style="font-size:11px!important;">
                        {{ $t['period_name'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tabel Detail --}}
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-center">Karyawan</th>
                        <th>Total Gaji</th>
                        <th>Rata-rata Gaji</th>
                        <th>Grafik Perbandingan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trend as $t)
                    @php $pct = ($t['total_salary'] / $maxSalary) * 100; @endphp
                    <tr>
                        <td class="fw-700">{{ $t['period_name'] }}</td>
                        <td class="text-center">{{ $t['total_employee'] }} orang</td>
                        <td class="fw-600 text-success">
                            {{ rupiah($t['total_salary']) }}
                        </td>
                        <td class="text-muted">
                            {{ rupiah($t['avg_salary']) }}
                        </td>
                        <td style="width:250px;">
                            <div style="background:#F0F0F0;border-radius:4px;height:10px;overflow:hidden;">
                                <div style="background:var(--primary);height:100%;width:{{ $pct }}%;border-radius:4px;"></div>
                            </div>
                            <div class="fs-13 text-muted mt-1">{{ number_format($pct, 1) }}%</div>
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

{{-- Distribusi per Kategori --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-pie-chart-fill"></i>
            Distribusi Gaji per Kategori (Periode Terbaru)
        </div>
    </div>
    <div class="card-body">
        @if(count($catDist) > 0)
        <div class="row g-3 mb-4">
            @foreach($catDist as $cat)
            @php
                $pct = $maxSalary > 0
                    ? (collect($catDist)->sum('total_salary') > 0
                        ? ($cat['total_salary'] / collect($catDist)->sum('total_salary') * 100)
                        : 0)
                    : 0;
                $colors = ['#FFC107', '#0D6EFD', '#198754', '#DC3545', '#6F42C1'];
                $color  = $colors[$loop->index % count($colors)];
            @endphp
            <div class="col-md-3">
                <div class="p-3 rounded text-center"
                     style="border:2px solid {{ $color }};border-radius:var(--radius)!important;">
                    <div class="fw-700" style="font-size:22px;color:{{ $color }};">
                        {{ $cat['total_employee'] }}
                    </div>
                    <div class="fw-600 fs-14 mt-1">{{ $cat['category_name'] }}</div>
                    <div class="fs-13 text-muted mt-1">{{ rupiah($cat['avg_salary']) }}/org</div>
                    <div class="mt-2" style="background:#F0F0F0;border-radius:4px;height:6px;">
                        <div style="background:{{ $color }};height:100%;width:{{ $pct }}%;border-radius:4px;"></div>
                    </div>
                    <div class="fs-13 text-muted mt-1">{{ number_format($pct, 1) }}% dari total</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Tabel Distribusi --}}
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah Karyawan</th>
                    <th>Total Gaji</th>
                    <th>Rata-rata</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSalaryAll = collect($catDist)->sum('total_salary'); @endphp
                @foreach($catDist as $cat)
                @php
                    $pctRow = $totalSalaryAll > 0
                        ? ($cat['total_salary'] / $totalSalaryAll * 100)
                        : 0;
                @endphp
                <tr>
                    <td class="fw-700">{{ $cat['category_name'] }}</td>
                    <td class="text-center">{{ $cat['total_employee'] }} orang</td>
                    <td class="fw-600 text-success">{{ rupiah($cat['total_salary']) }}</td>
                    <td class="text-muted">{{ rupiah($cat['avg_salary']) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1;background:#F0F0F0;border-radius:4px;height:8px;">
                                <div style="background:var(--primary);height:100%;width:{{ $pctRow }}%;border-radius:4px;"></div>
                            </div>
                            <span class="fs-13 fw-600" style="width:45px;">
                                {{ number_format($pctRow, 1) }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr style="background:#FFF3CD;">
                    <td class="fw-700">Total</td>
                    <td class="text-center fw-700">
                        {{ collect($catDist)->sum('total_employee') }} orang
                    </td>
                    <td class="fw-700 text-success">
                        {{ rupiah($totalSalaryAll) }}
                    </td>
                    <td></td>
                    <td class="fw-700">100%</td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🥧</div>
            <p>Belum ada data distribusi kategori.</p>
        </div>
        @endif
    </div>
</div>

@endsection