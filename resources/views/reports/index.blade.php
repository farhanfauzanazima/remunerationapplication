@extends('layouts.app')

@section('title', 'Laporan & Statistik')
@section('page-title', 'Laporan & Statistik')
@section('page-subtitle', 'Analisis data penggajian restoran')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
        <h1>Laporan & Statistik</h1>
        <p>Analisis data penggajian dan tren biaya karyawan</p>
    </div>
</div>

{{-- Menu Laporan --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="#rekap" class="stat-card text-decoration-none" style="flex-direction:column;align-items:flex-start;gap:12px;">
            <div class="stat-icon yellow">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
            </div>
            <div>
                <div class="fw-700 fs-14">Rekap Gaji Per Periode</div>
                <div class="fs-13 text-muted mt-1">
                    Lihat total gaji, breakdown per karyawan, export PDF
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.statistics') }}" class="stat-card text-decoration-none" style="flex-direction:column;align-items:flex-start;gap:12px;">
            <div class="stat-icon blue">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <div class="fw-700 fs-14">Statistik & Tren</div>
                <div class="fs-13 text-muted mt-1">
                    Tren biaya gaji, distribusi per kategori
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="#karyawan" class="stat-card text-decoration-none" style="flex-direction:column;align-items:flex-start;gap:12px;">
            <div class="stat-icon green">
                <i class="bi bi-person-lines-fill"></i>
            </div>
            <div>
                <div class="fw-700 fs-14">Laporan Per Karyawan</div>
                <div class="fs-13 text-muted mt-1">
                    Histori gaji individu, total diterima
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Rekap Gaji Per Periode --}}
<div class="card mb-4" id="rekap">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            Rekap Gaji Per Periode
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.salary-summary') }}"
              class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-600">Pilih Periode</label>
                <select name="period_id" class="form-select" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $period)
                    <option value="{{ $period['id'] }}">
                        {{ $period['period_name'] }}
                        ({{ $period['status'] === 'open' ? 'Aktif' : 'Closed' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary fw-600 w-100">
                    <i class="bi bi-search me-2"></i>Lihat Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Laporan Per Karyawan --}}
<div class="card mb-4" id="karyawan">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-person-lines-fill"></i>
            Laporan Per Karyawan
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="#" id="empReportForm"
              class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-600">Pilih Karyawan</label>
                <select name="employee_id"
                        class="form-select"
                        id="empSelect"
                        required>
                    <option value="">-- Pilih Karyawan --</option>
                    @php
                        $employees = app(\App\Services\ApiService::class)
                            ->get('/employees')['data'] ?? [];
                    @endphp
                    @foreach($employees as $emp)
                    <option value="{{ $emp['id'] }}">
                        {{ $emp['full_name'] }}
                        ({{ $emp['employee_code'] ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="button"
                        class="btn btn-primary fw-600 w-100"
                        onclick="goToEmployeeReport()">
                    <i class="bi bi-search me-2"></i>Lihat Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Statistik Ringkas --}}
@if(!empty($statistics['salary_trend']))
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-graph-up-arrow"></i>
            Tren Gaji ({{ count($statistics['salary_trend']) }} Periode Terakhir)
        </div>
        <a href="{{ route('reports.statistics') }}"
           class="btn btn-sm btn-primary fw-600">
            <i class="bi bi-arrow-right me-1"></i>Lihat Lengkap
        </a>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>Total Gaji</th>
                        <th>Rata-rata</th>
                        <th>Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxSalary = collect($statistics['salary_trend'])->max('total_salary') ?: 1;
                    @endphp
                    @foreach($statistics['salary_trend'] as $trend)
                    @php
                        $pct = ($trend['total_salary'] / $maxSalary) * 100;
                    @endphp
                    <tr>
                        <td class="fw-600">{{ $trend['period_name'] }}</td>
                        <td>{{ $trend['total_employee'] }} orang</td>
                        <td class="fw-600 text-success">
                            {{ rupiah($trend['total_salary']) }}
                        </td>
                        <td class="text-muted">
                            {{ rupiah($trend['avg_salary']) }}
                        </td>
                        <td style="width:200px;">
                            <div style="background:#F0F0F0;border-radius:4px;height:10px;overflow:hidden;">
                                <div style="background:var(--primary);height:100%;width:{{ $pct }}%;border-radius:4px;"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function goToEmployeeReport() {
    const empId = document.getElementById('empSelect').value;
    if (!empId) {
        alert('Pilih karyawan terlebih dahulu.');
        return;
    }
    window.location.href = '{{ url("/reports/employee") }}/' + empId;
}
</script>
@endpush