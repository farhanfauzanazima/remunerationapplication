@extends('layouts.app')

@section('title', 'Rekap Gaji')
@section('page-title', 'Rekap Gaji')
@section('page-subtitle', 'Laporan penggajian per periode')

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
                <li class="breadcrumb-item active">Rekap Gaji</li>
            </ol>
        </nav>
        <h1>Rekap Gaji: {{ $report['period']['period_name'] ?? '-' }}</h1>
        <p>
            {{ isset($report['period']['start_date'])
                ? \Carbon\Carbon::parse($report['period']['start_date'])->format('d M Y')
                : '-' }}
            s/d
            {{ isset($report['period']['end_date'])
                ? \Carbon\Carbon::parse($report['period']['end_date'])->format('d M Y')
                : '-' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        {{-- Export PDF --}}
        <a href="{{ route('reports.export-pdf', ['period_id' => $report['period']['id']]) }}"
           class="btn btn-danger fw-600">
            <i class="bi bi-file-pdf me-2"></i>Export PDF
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary fw-600">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

{{-- Ganti Periode --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.salary-summary') }}"
              class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fs-13 fw-600">Ganti Periode</label>
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periods as $period)
                    <option value="{{ $period['id'] }}"
                            {{ $report['period']['id'] == $period['id'] ? 'selected' : '' }}>
                        {{ $period['period_name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">
                    {{ $report['summary']['total_employees'] ?? 0 }}
                </div>
                <div class="stat-label">Total Karyawan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    {{ rupiah($report['summary']['total_salary'] ?? 0) }}
                </div>
                <div class="stat-label">Total Gaji Bersih</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    {{ rupiah($report['summary']['total_bonus'] ?? 0) }}
                </div>
                <div class="stat-label">Total Bonus</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-dash-circle-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    {{ rupiah($report['summary']['total_late_penalty'] ?? 0) }}
                </div>
                <div class="stat-label">Total Potongan</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Per Kategori --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-tags-fill"></i>
                    Breakdown per Kategori
                </div>
            </div>
            <div class="card-body p-0">
                @if(!empty($report['by_category']))
                <table class="table-custom w-100">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah</th>
                            <th>Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['by_category'] as $cat)
                        <tr>
                            <td class="fw-600">{{ $cat['category_name'] }}</td>
                            <td class="text-center">{{ $cat['total_employee'] }} org</td>
                            <td class="fw-600 text-success">
                                {{ rupiah($cat['total_salary']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Per Karyawan --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-list-ul"></i>
                    Detail Per Karyawan
                </div>
            </div>
            <div class="card-body p-0">
                @if(!empty($report['employees']))
                <div style="overflow-x:auto;">
                    <table class="table-custom w-100">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Kategori</th>
                                <th class="text-center">Hari</th>
                                <th class="text-center">Terlambat</th>
                                <th>Gaji Pokok</th>
                                <th>Bonus</th>
                                <th>Potongan</th>
                                <th>Total Bersih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['employees'] as $emp)
                            <tr>
                                <td>
                                    <div class="fw-700 fs-13">
                                        {{ $emp['full_name'] }}
                                    </div>
                                    <div class="fs-13 text-muted">
                                        {{ $emp['employee_code'] ?? '-' }}
                                    </div>
                                </td>
                                <td class="fs-13">{{ $emp['category'] }}</td>
                                <td class="text-center">
                                    {{ $emp['total_working_days'] }}
                                </td>
                                <td class="text-center text-danger">
                                    {{ $emp['late_count'] > 0 ? $emp['late_count'] . 'x' : '-' }}
                                </td>
                                <td>{{ rupiah($emp['base_salary_amount']) }}</td>
                                <td class="text-success">
                                    {{ $emp['bonus'] > 0 ? rupiah($emp['bonus']) : '-' }}
                                </td>
                                <td class="text-danger">
                                    @php
                                        $potongan = ($emp['late_penalty_amount'] ?? 0)
                                            + ($emp['additional_deduction'] ?? 0);
                                    @endphp
                                    {{ $potongan > 0 ? rupiah($potongan) : '-' }}
                                </td>
                                <td class="fw-700 text-success">
                                    {{ rupiah($emp['total_salary']) }}
                                </td>
                                <td>{!! statusBadge($emp['status']) !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#FFF3CD;">
                                <td colspan="7" class="fw-700 text-end pe-3 py-3">
                                    Total Keseluruhan:
                                </td>
                                <td class="fw-700 text-success py-3">
                                    {{ rupiah($report['summary']['total_salary'] ?? 0) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <p>Belum ada data slip gaji di periode ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection