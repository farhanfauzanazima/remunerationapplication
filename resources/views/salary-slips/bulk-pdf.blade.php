@extends('layouts.app')

@section('title', 'Bulk Generate PDF')
@section('page-title', 'Generate PDF Massal')
@section('page-subtitle', 'Generate PDF slip gaji untuk semua karyawan dalam satu periode')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('salary-slips.index') }}">Slip Gaji</a>
                </li>
                <li class="breadcrumb-item active">Bulk Generate PDF</li>
            </ol>
        </nav>
        <h1>Generate PDF Massal</h1>
        <p>Generate PDF slip gaji untuk seluruh karyawan dalam periode</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-file-pdf"></i>
                    Form Bulk Generate PDF
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('pdf.bulk-generate') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-600">
                            Pilih Periode <span class="text-danger">*</span>
                        </label>
                        <select name="period_id"
                                class="form-select"
                                required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periods as $period)
                            <option value="{{ $period['id'] }}"
                                    {{ request('period_id') == $period['id'] ? 'selected' : '' }}>
                                {{ $period['period_name'] }}
                                ({{ $period['status'] === 'open' ? 'Aktif' : 'Closed' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert-custom alert-info mb-4">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Sistem akan generate PDF untuk semua slip gaji dalam periode yang dipilih. Proses ini mungkin membutuhkan beberapa detik.</span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger fw-600">
                            <i class="bi bi-file-pdf me-2"></i>Generate Semua PDF
                        </button>
                        <a href="{{ route('salary-slips.index') }}"
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