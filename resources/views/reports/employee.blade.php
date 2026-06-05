@extends('layouts.app')

@section('title', 'Laporan Karyawan')
@section('page-title', 'Laporan Per Karyawan')
@section('page-subtitle', 'Histori gaji individu karyawan')

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
                <li class="breadcrumb-item active">Per Karyawan</li>
            </ol>
        </nav>
        <h1>Laporan: {{ $report['employee']['full_name'] ?? '-' }}</h1>
        <p>
            {{ $report['employee']['employee_code'] ?? '-' }} •
            {{ $report['employee']['category'] ?? '-' }} •
            {!! statusBadge($report['employee']['status'] ?? 'active') !!}
        </p>
    </div>
    <div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary fw-600">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

{{-- Ganti Karyawan --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end"
              onsubmit="event.preventDefault(); goToEmpReport()">
            <div class="col-md-4">
                <label class="form-label fs-13 fw-600">Ganti Karyawan</label>
                <select class="form-select" id="empSelect2">
                    @foreach($employees as $emp)
                    <option value="{{ $emp['id'] }}"
                            {{ $report['employee']['id'] == $emp['id'] ? 'selected' : '' }}>
                        {{ $emp['full_name'] }} ({{ $emp['employee_code'] ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="button"
                        class="btn btn-primary fw-600 w-100"
                        onclick="goToEmpReport()">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">
                    {{ $report['summary']['total_periods'] ?? 0 }}
                </div>
                <div class="stat-label">Total Periode</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    {{ rupiah($report['summary']['total_received'] ?? 0) }}
                </div>
                <div class="stat-label">Total Diterima</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-calculator-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    {{ rupiah($report['summary']['average_salary'] ?? 0) }}
                </div>
                <div class="stat-label">Rata-rata Gaji</div>
            </div>
        </div>
    </div>
</div>

{{-- Histori --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-clock-history"></i>
            Histori Penggajian
        </div>
    </div>
    <div class="card-body p-0">
        @php $slips = $report['salary_history'] ?? []; @endphp
        @if(count($slips) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Hari Masuk</th>
                        <th>Terlambat</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Bonus</th>
                        <th>Potongan</th>
                        <th>Total Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slips as $slip)
                    <tr>
                        <td class="fw-700">{{ $slip['period'] ?? '-' }}</td>
                        <td class="text-center">{{ $slip['total_working_days'] }} hari</td>
                        <td class="text-center text-danger">
                            {{ $slip['late_count'] > 0 ? $slip['late_count'] . 'x' : '-' }}
                        </td>
                        <td>{{ rupiah($slip['base_salary']) }}</td>
                        <td>{{ rupiah($slip['allowance']) }}</td>
                        <td class="text-success">
                            {{ ($slip['bonus'] ?? 0) > 0 ? rupiah($slip['bonus']) : '-' }}
                        </td>
                        <td class="text-danger">
                            @php
                                $pot = ($slip['late_penalty'] ?? 0) + ($slip['deduction'] ?? 0);
                            @endphp
                            {{ $pot > 0 ? rupiah($pot) : '-' }}
                        </td>
                        <td class="fw-700 text-success">
                            {{ rupiah($slip['total_salary']) }}
                        </td>
                        <td>{!! statusBadge($slip['status']) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#FFF3CD;">
                        <td colspan="7" class="fw-700 text-end pe-3 py-3">
                            Total Diterima:
                        </td>
                        <td class="fw-700 text-success py-3">
                            {{ rupiah(collect($slips)->sum('total_salary')) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📄</div>
            <p>Belum ada riwayat gaji untuk karyawan ini.</p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function goToEmpReport() {
    const id = document.getElementById('empSelect2').value;
    window.location.href = '{{ url("/reports/employee") }}/' + id;
}
</script>
@endpush