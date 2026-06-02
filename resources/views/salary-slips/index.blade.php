@extends('layouts.app')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-subtitle', 'Kelola slip gaji karyawan')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Slip Gaji</li>
            </ol>
        </nav>
        <h1>Slip Gaji</h1>
        <p>Total {{ count($slips) }} slip ditemukan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('salary-slips.bulk-create') }}"
           class="btn btn-outline-secondary fw-600">
            <i class="bi bi-people-fill me-2"></i>Input Massal
        </a>
        <a href="{{ route('salary-slips.create') }}"
           class="btn btn-primary fw-600">
            <i class="bi bi-plus-circle me-2"></i>Input Slip
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('salary-slips.index') }}"
              class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Periode</label>
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $period)
                    <option value="{{ $period['id'] }}"
                            {{ request('period_id') == $period['id'] ? 'selected' : '' }}>
                        {{ $period['period_name'] }}
                        {{ $period['status'] === 'open' ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp['id'] }}"
                            {{ request('employee_id') == $emp['id'] ? 'selected' : '' }}>
                        {{ $emp['full_name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>
                        Terkirim
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-600 flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('salary-slips.index') }}"
                       class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($error)
<div class="alert-custom alert-error mb-4">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $error }}
</div>
@endif

{{-- Summary Cards --}}
@if(count($slips) > 0)
@php
    $totalSalary = collect($slips)->sum('total_salary');
    $totalDraft  = collect($slips)->where('status', 'draft')->count();
    $totalSent   = collect($slips)->where('status', 'sent')->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ count($slips) }}</div>
                <div class="stat-label">Total Slip</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:16px;">
                    {{ rupiah($totalSalary) }}
                </div>
                <div class="stat-label">Total Gaji Bersih</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalSent }}/{{ count($slips) }}</div>
                <div class="stat-label">Sudah Terkirim</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabel Slip --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($slips) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Kategori</th>
                        <th>Hari Kerja</th>
                        <th>Terlambat</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slips as $i => $slip)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-700">
                                {{ $slip['employee']['full_name'] ?? '-' }}
                            </div>
                            <div class="fs-13 text-muted">
                                {{ $slip['employee']['employee_code'] ?? '-' }}
                            </div>
                        </td>
                        <td class="fs-14">
                            {{ $slip['period']['period_name'] ?? '-' }}
                        </td>
                        <td>
                            <span class="badge-custom badge-open">
                                {{ $slip['category']['category_name'] ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center fw-600">
                            {{ $slip['total_working_days'] }} hari
                        </td>
                        <td class="text-center">
                            @if($slip['late_count'] > 0)
                            <span class="text-danger fw-600">
                                {{ $slip['late_count'] }}x
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-700 text-success">
                            {{ rupiah($slip['total_salary']) }}
                        </td>
                        <td>{!! statusBadge($slip['status']) !!}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('salary-slips.show', $slip['id']) }}"
                                   class="btn-action view"
                                   title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($slip['status'] === 'draft')
                                <a href="{{ route('salary-slips.edit', $slip['id']) }}"
                                   class="btn-action edit"
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button"
                                        class="btn-action delete"
                                        title="Hapus"
                                        onclick="confirmDelete({{ $slip['id'] }}, '{{ $slip['employee']['full_name'] ?? '' }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                @endif
                            </div>

                            <form id="deleteForm-{{ $slip['id'] }}"
                                  action="{{ route('salary-slips.destroy', $slip['id']) }}"
                                  method="POST"
                                  style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📄</div>
            <h5>Belum Ada Slip Gaji</h5>
            <p>Mulai buat slip gaji untuk periode yang aktif.</p>
            <div class="d-flex gap-2 justify-content-center mt-3">
                <a href="{{ route('salary-slips.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Input Single
                </a>
                <a href="{{ route('salary-slips.bulk-create') }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-people me-2"></i>Input Massal
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm('Hapus slip gaji milik "' + name + '"?')) {
        document.getElementById('deleteForm-' + id).submit();
    }
}
</script>
@endpush