@extends('layouts.app')

@section('title', 'Edit Periode')
@section('page-title', 'Edit Periode Penggajian')
@section('page-subtitle', 'Perbarui data periode penggajian')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Edit: {{ $period['period_name'] }}</h1>
        <p>Status: {!! statusBadge($period['status']) !!}</p>
    </div>
</div>

@if($period['status'] === 'closed')
<div class="alert-custom alert-warning mb-4">
    <i class="bi bi-lock-fill"></i>
    Periode ini sudah ditutup dan tidak dapat diedit.
    <a href="{{ route('periods.index') }}" class="fw-600 ms-2">Kembali</a>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pencil-fill"></i>
                    Form Edit Periode
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('periods.update', $period['id']) }}"
                      method="POST">
                    @csrf
                    @method('PUT')

                    <fieldset {{ $period['status'] === 'closed' ? 'disabled' : '' }}>
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    Nama Periode <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="period_name"
                                       class="form-control @error('period_name') is-invalid @enderror"
                                       value="{{ old('period_name', $period['period_name']) }}"
                                       required>
                                @error('period_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="start_date"
                                       id="startDate"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', \Carbon\Carbon::parse($period['start_date'])->format('Y-m-d')) }}"
                                       required
                                       onchange="calcDuration()">
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal Akhir <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="end_date"
                                       id="endDate"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date', \Carbon\Carbon::parse($period['end_date'])->format('Y-m-d')) }}"
                                       required
                                       onchange="calcDuration()">
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded"
                                     style="background:#F8F9FA;border:1px solid var(--border);">
                                    <div class="fs-13 text-muted mb-1">Durasi Periode:</div>
                                    <div class="fw-700" id="durationText"
                                         style="color:var(--primary);">—</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes"
                                          class="form-control"
                                          rows="2">{{ old('notes', $period['notes']) }}</textarea>
                            </div>

                        </div>

                        @if($period['status'] !== 'closed')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-600">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('periods.index') }}"
                               class="btn btn-outline-secondary fw-600">
                                <i class="bi bi-x me-2"></i>Batal
                            </a>
                        </div>
                        @endif
                    </fieldset>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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

document.addEventListener('DOMContentLoaded', calcDuration);
</script>
@endpush