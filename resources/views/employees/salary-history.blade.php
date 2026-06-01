@extends('layouts.app')

@section('title', 'Riwayat Gaji')
@section('page-title', 'Riwayat Gaji Karyawan')
@section('page-subtitle', 'Histori penggajian karyawan')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}">Karyawan</a>
                </li>
                <li class="breadcrumb-item active">Riwayat Gaji</li>
            </ol>
        </nav>
        <h1>Riwayat Gaji: {{ $employee['full_name'] }}</h1>
        <p>{{ $employee['employee_code'] ?? '-' }} •
           {{ $employee['category']['category_name'] ?? '-' }} •
           {!! statusBadge($employee['status']) !!}
        </p>
    </div>
    <div>
        <a href="{{ route('employees.edit', $employee['id']) }}"
           class="btn btn-outline-secondary fw-600">
            <i class="bi bi-pencil me-2"></i>Edit Karyawan
        </a>
    </div>
</div>

{{-- Info Karyawan --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="fs-13 text-muted">Email</div>
                <div class="fw-600">{{ $employee['email'] }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Telepon</div>
                <div class="fw-600">{{ $employee['phone'] }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Bergabung</div>
                <div class="fw-600">
                    {{ isset($employee['join_date'])
                        ? \Carbon\Carbon::parse($employee['join_date'])->format('d M Y')
                        : '-' }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Total Periode Gaji</div>
                <div class="fw-700" style="font-size:20px;color:var(--primary);">
                    {{ count($history['salary_history'] ?? []) }}x
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Riwayat --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-clock-history"></i>
            Histori Penggajian
        </div>
    </div>
    <div class="card-body p-0">
        @php $slips = $history['salary_history'] ?? []; @endphp

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
                        <td class="fw-600">{{ $slip['period']['period_name'] ?? '-' }}</td>
                        <td class="text-center">{{ $slip['total_working_days'] }} hari</td>
                        <td class="text-center text-danger">
                            {{ $slip['late_count'] }}x
                        </td>
                        <td>{{ rupiah($slip['base_salary']) }}</td>
                        <td>{{ rupiah($slip['allowance']) }}</td>
                        <td class="text-success">
                            {{ $slip['bonus'] > 0 ? rupiah($slip['bonus']) : '-' }}
                        </td>
                        <td class="text-danger">
                            @php
                                $totalDeduction = ($slip['late_penalty'] ?? 0) + ($slip['deduction'] ?? 0);
                            @endphp
                            {{ $totalDeduction > 0 ? rupiah($totalDeduction) : '-' }}
                        </td>
                        <td class="fw-700 text-success">
                            {{ rupiah($slip['total_salary']) }}
                        </td>
                        <td>{!! statusBadge($slip['status']) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#F8F9FA;">
                        <td colspan="7" class="fw-700 text-end pe-3">
                            Total Diterima:
                        </td>
                        <td class="fw-700 text-success">
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
            <h5>Belum Ada Riwayat Gaji</h5>
            <p>Karyawan ini belum memiliki slip gaji yang tercatat.</p>
        </div>
        @endif
    </div>
</div>

@endsection