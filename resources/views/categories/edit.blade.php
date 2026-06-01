@extends('layouts.app')

@section('title', 'Edit Kategori Gaji')
@section('page-title', 'Edit Kategori Gaji')
@section('page-subtitle', 'Perbarui data kategori gaji')

@section('content')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Edit: {{ $category['category_name'] }}</h1>
        <p>Perbarui komponen gaji untuk kategori ini</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pencil-fill"></i>
                    Form Edit Kategori
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('categories.update', $category['id']) }}"
                      method="POST"
                      id="categoryForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="category_name"
                                   class="form-control @error('category_name') is-invalid @enderror"
                                   value="{{ old('category_name', $category['category_name']) }}"
                                   required>
                            @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                                       value="{{ old('base_salary', $category['base_salary']) }}"
                                       min="0"
                                       step="1000"
                                       required
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Tetap (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="allowance"
                                       id="allowance"
                                       class="form-control"
                                       value="{{ old('allowance', $category['allowance']) }}"
                                       min="0"
                                       step="1000"
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Potongan per Keterlambatan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="late_penalty"
                                       id="latePenalty"
                                       class="form-control"
                                       value="{{ old('late_penalty', $category['late_penalty']) }}"
                                       min="0"
                                       step="1000"
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Rate Lembur per Jam (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="overtime_rate"
                                       class="form-control"
                                       value="{{ old('overtime_rate', $category['overtime_rate']) }}"
                                       min="0"
                                       step="1000">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="2">{{ old('description', $category['description']) }}</textarea>
                        </div>

                        {{-- Status Aktif --}}
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       id="isActive"
                                       value="1"
                                       {{ $category['is_active'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-600" for="isActive">
                                    Kategori Aktif
                                </label>
                            </div>
                            <div class="form-text">
                                Nonaktifkan jika kategori ini tidak lagi digunakan
                            </div>
                        </div>

                    </div>

                    {{-- Preview --}}
                    <div class="mt-4 p-3 rounded" style="background:#FFF3CD;border:1px solid #FFC107;">
                        <div class="fw-700 mb-2">
                            <i class="bi bi-calculator me-2"></i>Preview Gaji Dasar
                        </div>
                        <div class="row g-2 fs-14">
                            <div class="col-md-4">
                                <span class="text-muted">Gaji Pokok:</span>
                                <span class="fw-600 ms-2" id="previewBase">
                                    {{ rupiah($category['base_salary']) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">+ Tunjangan:</span>
                                <span class="fw-600 ms-2" id="previewAllowance">
                                    {{ rupiah($category['allowance']) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">Total:</span>
                                <span class="fw-700 ms-2 text-success" id="previewTotal">
                                    {{ rupiah($category['base_salary'] + $category['allowance']) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-600">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
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
</script>
@endpush