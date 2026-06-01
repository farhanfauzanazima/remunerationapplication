@extends('layouts.app')

@section('title', 'Tambah Kategori Gaji')
@section('page-title', 'Tambah Kategori Gaji')
@section('page-subtitle', 'Buat kategori gaji baru')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categories.index') }}">Kategori Gaji</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
        <h1>Tambah Kategori Gaji</h1>
        <p>Isi form berikut untuk membuat kategori gaji baru</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-tags-fill"></i>
                    Form Kategori Gaji
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                    @csrf

                    <div class="row g-3">

                        {{-- Nama Kategori --}}
                        <div class="col-12">
                            <label class="form-label">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="category_name"
                                   class="form-control @error('category_name') is-invalid @enderror"
                                   value="{{ old('category_name') }}"
                                   placeholder="Contoh: Kategori 1, Magang, Karyawan Tetap"
                                   required>
                            @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gaji Pokok --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Gaji Pokok (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="base_salary"
                                       id="baseSalary"
                                       class="form-control @error('base_salary') is-invalid @enderror"
                                       value="{{ old('base_salary', 0) }}"
                                       min="0"
                                       step="1000"
                                       required
                                       oninput="updatePreview()">
                            </div>
                            @error('base_salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tunjangan --}}
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Tetap (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="allowance"
                                       id="allowance"
                                       class="form-control"
                                       value="{{ old('allowance', 0) }}"
                                       min="0"
                                       step="1000"
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        {{-- Potongan Terlambat --}}
                        <div class="col-md-6">
                            <label class="form-label">Potongan per Keterlambatan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="late_penalty"
                                       id="latePenalty"
                                       class="form-control"
                                       value="{{ old('late_penalty', 0) }}"
                                       min="0"
                                       step="1000"
                                       oninput="updatePreview()">
                            </div>
                            <div class="form-text">Jumlah potongan untuk setiap 1x keterlambatan</div>
                        </div>

                        {{-- Rate Lembur --}}
                        <div class="col-md-6">
                            <label class="form-label">Rate Lembur per Jam (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="overtime_rate"
                                       class="form-control"
                                       value="{{ old('overtime_rate', 0) }}"
                                       min="0"
                                       step="1000">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Keterangan tambahan tentang kategori ini...">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    {{-- Preview Kalkulasi --}}
                    <div class="mt-4 p-3 rounded" style="background:#FFF3CD;border:1px solid #FFC107;">
                        <div class="fw-700 mb-2">
                            <i class="bi bi-calculator me-2"></i>Preview Gaji Dasar
                        </div>
                        <div class="row g-2 fs-14">
                            <div class="col-md-4">
                                <span class="text-muted">Gaji Pokok:</span>
                                <span class="fw-600 ms-2" id="previewBase">Rp 0</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">+ Tunjangan:</span>
                                <span class="fw-600 ms-2" id="previewAllowance">Rp 0</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">Total Tanpa Potongan:</span>
                                <span class="fw-700 ms-2 text-success" id="previewTotal">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-600">
                            <i class="bi bi-check-lg me-2"></i>Simpan Kategori
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary fw-600">
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
function formatRp(val) {
    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
}

function updatePreview() {
    const base      = parseInt(document.getElementById('baseSalary').value) || 0;
    const allowance = parseInt(document.getElementById('allowance').value) || 0;
    const total     = base + allowance;

    document.getElementById('previewBase').textContent      = formatRp(base);
    document.getElementById('previewAllowance').textContent = formatRp(allowance);
    document.getElementById('previewTotal').textContent     = formatRp(total);
}

// Init
updatePreview();
</script>
@endpush