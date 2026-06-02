@extends('layouts.app')

@section('title', 'Tambah Periode')
@section('page-title', 'Tambah Periode Penggajian')
@section('page-subtitle', 'Buat periode penggajian baru')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('periods.index') }}">Periode</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
        <h1>Tambah Periode Penggajian</h1>
        <p>Tentukan nama dan rentang tanggal periode penggajian</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-calendar-plus-fill"></i>
                    Form Periode Penggajian
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('periods.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- Nama Periode --}}
                        <div class="col-12">
                            <label class="form-label">
                                Nama Periode <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="period_name"
                                   id="periodName"
                                   class="form-control @error('period_name') is-invalid @enderror"
                                   value="{{ old('period_name') }}"
                                   placeholder="Contoh: Juni 2026"
                                   required>
                            <div class="form-text">
                                Nama periode akan tampil di slip gaji karyawan
                            </div>
                            @error('period_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="start_date"
                                   id="startDate"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', date('Y-m-01')) }}"
                                   required
                                   onchange="autoFillPeriodName(); calcDuration()">
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Akhir --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Tanggal Akhir <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="end_date"
                                   id="endDate"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', date('Y-m-t')) }}"
                                   required
                                   onchange="calcDuration()">
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Duration Preview --}}
                        <div class="col-12">
                            <div class="p-3 rounded"
                                 style="background:#F8F9FA;border:1px solid var(--border);">
                                <div class="fs-13 text-muted mb-1">Durasi Periode:</div>
                                <div class="fw-700" id="durationText" style="color:var(--primary);">
                                    —
                                </div>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Catatan tambahan (opsional)...">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-600">
                            <i class="bi bi-check-lg me-2"></i>Buat Periode
                        </button>
                        <a href="{{ route('periods.index') }}"
                           class="btn btn-outline-secondary fw-600">
                            <i class="bi bi-x me-2"></i>Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const MONTHS_ID = [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

function autoFillPeriodName() {
    const startDate  = document.getElementById('startDate').value;
    const periodName = document.getElementById('periodName');

    if (startDate && !periodName.value) {
        const d     = new Date(startDate);
        const month = MONTHS_ID[d.getMonth()];
        const year  = d.getFullYear();
        periodName.value = month + ' ' + year;
    }
}

function calcDuration() {
    const start = document.getElementById('startDate').value;
    const end   = document.getElementById('endDate').value;
    const text  = document.getElementById('durationText');

    if (start && end) {
        const s    = new Date(start);
        const e    = new Date(end);
        const days = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;

        if (days > 0) {
            const sStr = s.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
            const eStr = e.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
            text.textContent = days + ' hari (' + sStr + ' s/d ' + eStr + ')';
            text.style.color = 'var(--primary)';
        } else {
            text.textContent = 'Tanggal akhir harus setelah tanggal mulai';
            text.style.color = '#DC3545';
        }
    }
}

// Init
document.addEventListener('DOMContentLoaded', function () {
    calcDuration();
    autoFillPeriodName();
});
</script>
@endpush