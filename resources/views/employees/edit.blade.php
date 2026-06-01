@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')
@section('page-subtitle', 'Perbarui data karyawan')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Edit: {{ $employee['full_name'] }}</h1>
        <p>Kode: {{ $employee['employee_code'] ?? '-' }}</p>
    </div>
    <div>
        <a href="{{ route('employees.salary-history', $employee['id']) }}"
           class="btn btn-outline-secondary fw-600">
            <i class="bi bi-clock-history me-2"></i>Riwayat Gaji
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pencil-fill"></i>
                    Form Edit Karyawan
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('employees.update', $employee['id']) }}"
                      method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="full_name"
                                   class="form-control @error('full_name') is-invalid @enderror"
                                   value="{{ old('full_name', $employee['full_name']) }}"
                                   required>
                            @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kode Karyawan</label>
                            <input type="text"
                                   name="employee_code"
                                   class="form-control"
                                   value="{{ old('employee_code', $employee['employee_code']) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Kategori Gaji <span class="text-danger">*</span>
                            </label>
                            <select name="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror"
                                    id="categorySelect"
                                    onchange="showCategoryInfo()"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}"
                                        data-base="{{ $cat['base_salary'] }}"
                                        data-allowance="{{ $cat['allowance'] }}"
                                        data-penalty="{{ $cat['late_penalty'] }}"
                                        {{ old('category_id', $employee['category']['id'] ?? '') == $cat['id'] ? 'selected' : '' }}>
                                    {{ $cat['category_name'] }}
                                    ({{ rupiah($cat['base_salary']) }})
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="active"
                                        {{ old('status', $employee['status']) === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="inactive"
                                        {{ old('status', $employee['status']) === 'inactive' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $employee['email']) }}"
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-phone"></i>
                                </span>
                                <input type="text"
                                       name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $employee['phone']) }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Bergabung</label>
                            <input type="date"
                                   name="join_date"
                                   class="form-control"
                                   value="{{ old('join_date', $employee['join_date']
                                       ? \Carbon\Carbon::parse($employee['join_date'])->format('Y-m-d')
                                       : '') }}">
                        </div>

                    </div>

                    {{-- Info Kategori --}}
                    <div id="categoryInfo"
                         class="mt-4 p-3 rounded"
                         style="background:#FFF3CD;border:1px solid #FFC107;">
                        <div class="fw-700 mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                            Komponen Gaji Kategori Terpilih
                        </div>
                        <div class="row g-2 fs-14">
                            <div class="col-md-4">
                                <span class="text-muted">Gaji Pokok:</span>
                                <span class="fw-600 ms-2" id="catBase">-</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">Tunjangan:</span>
                                <span class="fw-600 ms-2" id="catAllowance">-</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">Potongan Terlambat:</span>
                                <span class="fw-600 ms-2 text-danger" id="catPenalty">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-600">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('employees.index') }}"
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
function formatRp(val) {
    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
}

function showCategoryInfo() {
    const select = document.getElementById('categorySelect');
    const option = select.options[select.selectedIndex];

    if (select.value) {
        document.getElementById('catBase').textContent      = formatRp(option.dataset.base);
        document.getElementById('catAllowance').textContent = formatRp(option.dataset.allowance);
        document.getElementById('catPenalty').textContent   = formatRp(option.dataset.penalty) + '/x';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    showCategoryInfo();
});
</script>
@endpush