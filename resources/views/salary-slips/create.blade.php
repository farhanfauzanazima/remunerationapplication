@extends('layouts.app')

@section('title', 'Input Slip Gaji')
@section('page-title', 'Input Slip Gaji')
@section('page-subtitle', 'Buat slip gaji untuk satu karyawan')

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
                <li class="breadcrumb-item active">Input Single</li>
            </ol>
        </nav>
        <h1>Input Slip Gaji</h1>
        <p>Sistem akan menghitung gaji secara otomatis</p>
    </div>
    <a href="{{ route('salary-slips.bulk-create') }}"
       class="btn btn-outline-secondary fw-600">
        <i class="bi bi-people-fill me-2"></i>Beralih ke Input Massal
    </a>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    Form Input Gaji
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                @if(count($periods) === 0)
                <div class="alert-custom alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Tidak ada periode aktif (open). Minta Kepala Toko untuk membuka periode.
                </div>
                @endif

                <form action="{{ route('salary-slips.store') }}"
                      method="POST"
                      id="slipForm">
                    @csrf

                    <div class="row g-3">

                        {{-- Periode --}}
                        <div class="col-12">
                            <label class="form-label">
                                Periode Penggajian <span class="text-danger">*</span>
                            </label>
                            <select name="period_id"
                                    class="form-select @error('period_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periods as $period)
                                <option value="{{ $period['id'] }}"
                                        {{ old('period_id', $selectedPeriod) == $period['id']
                                            ? 'selected' : '' }}>
                                    {{ $period['period_name'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('period_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Karyawan --}}
                        <div class="col-12">
                            <label class="form-label">
                                Karyawan <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id"
                                    class="form-select @error('employee_id') is-invalid @enderror"
                                    id="employeeSelect"
                                    onchange="onEmployeeChange()"
                                    required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp['id'] }}"
                                        data-category-id="{{ $emp['category']['id'] ?? '' }}"
                                        data-category-name="{{ $emp['category']['category_name'] ?? '-' }}"
                                        data-base="{{ $emp['category']['base_salary'] ?? 0 }}"
                                        data-allowance="{{ $emp['category']['allowance'] ?? 0 }}"
                                        data-penalty="{{ $emp['category']['late_penalty'] ?? 0 }}"
                                        {{ old('employee_id') == $emp['id'] ? 'selected' : '' }}>
                                    {{ $emp['full_name'] }}
                                    ({{ $emp['employee_code'] ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori — READONLY, otomatis dari karyawan --}}
                        <div class="col-12">
                            <label class="form-label">
                                Kategori Gaji
                                <span class="text-muted fs-13 fw-400 ms-1">
                                    (otomatis dari data karyawan)
                                </span>
                            </label>

                            {{-- Hidden input yang akan dikirim ke server --}}
                            <input type="hidden"
                                   name="category_id"
                                   id="categoryIdInput"
                                   value="{{ old('category_id') }}">

                            {{-- Tampilan readonly --}}
                            <div class="form-control"
                                 id="categoryDisplay"
                                 style="background:#F8F9FA;color:#6c757d;cursor:not-allowed;">
                                -- Pilih karyawan terlebih dahulu --
                            </div>

                            @error('category_id')
                            <div class="text-danger fs-13 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hari Kerja --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Jumlah Hari Masuk <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       name="total_working_days"
                                       id="workingDays"
                                       class="form-control @error('total_working_days') is-invalid @enderror"
                                       value="{{ old('total_working_days', 0) }}"
                                       min="0" max="31"
                                       required
                                       oninput="updatePreview()">
                                <span class="input-group-text">hari</span>
                            </div>
                            @error('total_working_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Keterlambatan --}}
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Keterlambatan</label>
                            <div class="input-group">
                                <input type="number"
                                       name="late_count"
                                       id="lateCount"
                                       class="form-control"
                                       value="{{ old('late_count', 0) }}"
                                       min="0"
                                       oninput="updatePreview()">
                                <span class="input-group-text">kali</span>
                            </div>
                        </div>

                        {{-- Bonus --}}
                        <div class="col-md-6">
                            <label class="form-label">Bonus Tambahan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="bonus"
                                       id="bonus"
                                       class="form-control"
                                       value="{{ old('bonus', 0) }}"
                                       min="0" step="1000"
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        {{-- Potongan Tambahan --}}
                        <div class="col-md-6">
                            <label class="form-label">Potongan Tambahan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       name="additional_deduction"
                                       id="additionalDeduction"
                                       class="form-control"
                                       value="{{ old('additional_deduction', 0) }}"
                                       min="0" step="1000"
                                       oninput="updatePreview()">
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit"
                                class="btn btn-primary fw-600"
                                id="submitBtn"
                                {{ count($periods) === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check-lg me-2"></i>Buat Slip Gaji
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

    {{-- Preview Kalkulasi --}}
    <div class="col-md-5">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-calculator-fill"></i>
                    Preview Kalkulasi Gaji
                </div>
            </div>
            <div class="card-body">
                <table class="w-100" style="font-size:14px;">
                    <tr>
                        <td class="py-2 text-muted">Gaji Pokok</td>
                        <td class="py-2 text-end fw-600" id="pvBase">-</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-muted">Tunjangan</td>
                        <td class="py-2 text-end fw-600" id="pvAllowance">-</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-muted">Bonus</td>
                        <td class="py-2 text-end fw-600 text-success" id="pvBonus">-</td>
                    </tr>
                    <tr style="border-top:2px dashed #dee2e6;">
                        <td class="py-2 fw-600">Subtotal</td>
                        <td class="py-2 text-end fw-700" id="pvSubtotal">-</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-muted" id="pvLateLabel">
                            Potongan Terlambat (0x)
                        </td>
                        <td class="py-2 text-end fw-600 text-danger" id="pvLate">-</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-muted">Potongan Tambahan</td>
                        <td class="py-2 text-end fw-600 text-danger" id="pvDeduction">-</td>
                    </tr>
                    <tr style="border-top:2px solid var(--primary);background:#FFF3CD;">
                        <td class="py-3 fw-700" style="padding-left:8px;">
                            TOTAL GAJI BERSIH
                        </td>
                        <td class="py-3 text-end fw-700 text-success"
                            id="pvTotal"
                            style="font-size:18px;padding-right:8px;">
                            -
                        </td>
                    </tr>
                </table>

                <div class="mt-3 p-2 rounded fs-13 text-muted"
                     style="background:#F8F9FA;">
                    <i class="bi bi-info-circle me-1"></i>
                    Kategori gaji mengikuti data karyawan yang dipilih
                    dan tidak dapat diubah di sini.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// State kategori aktif
let activeCategory = null;

function formatRp(val) {
    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
}

function onEmployeeChange() {
    const select = document.getElementById('employeeSelect');
    const option = select.options[select.selectedIndex];

    if (!select.value) {
        // Reset jika tidak ada karyawan dipilih
        activeCategory = null;
        document.getElementById('categoryIdInput').value   = '';
        document.getElementById('categoryDisplay').textContent =
            '-- Pilih karyawan terlebih dahulu --';
        document.getElementById('categoryDisplay').style.color = '#6c757d';
        resetPreview();
        return;
    }

    // Ambil data kategori dari option karyawan
    activeCategory = {
        id:        option.dataset.categoryId,
        name:      option.dataset.categoryName,
        base:      parseFloat(option.dataset.base)      || 0,
        allowance: parseFloat(option.dataset.allowance) || 0,
        penalty:   parseFloat(option.dataset.penalty)   || 0,
    };

    // Set hidden input category_id
    document.getElementById('categoryIdInput').value = activeCategory.id;

    // Tampilkan kategori sebagai teks readonly
    document.getElementById('categoryDisplay').textContent =
        activeCategory.name +
        ' (Gaji Pokok: ' + formatRp(activeCategory.base) + ')';
    document.getElementById('categoryDisplay').style.color = '#212529';

    // Update preview
    updatePreview();
}

function updatePreview() {
    if (!activeCategory) {
        resetPreview();
        return;
    }

    const lateCount = parseInt(document.getElementById('lateCount').value)           || 0;
    const bonus     = parseFloat(document.getElementById('bonus').value)             || 0;
    const deduction = parseFloat(document.getElementById('additionalDeduction').value) || 0;

    const latePenalty = lateCount * activeCategory.penalty;
    const subtotal    = activeCategory.base + activeCategory.allowance + bonus;
    const total       = Math.max(0, subtotal - latePenalty - deduction);

    document.getElementById('pvBase').textContent      = formatRp(activeCategory.base);
    document.getElementById('pvAllowance').textContent = formatRp(activeCategory.allowance);
    document.getElementById('pvBonus').textContent     = formatRp(bonus);
    document.getElementById('pvSubtotal').textContent  = formatRp(subtotal);
    document.getElementById('pvLateLabel').textContent =
        'Potongan Terlambat (' + lateCount + 'x)';
    document.getElementById('pvLate').textContent      = '- ' + formatRp(latePenalty);
    document.getElementById('pvDeduction').textContent = '- ' + formatRp(deduction);
    document.getElementById('pvTotal').textContent     = formatRp(total);
}

function resetPreview() {
    ['pvBase','pvAllowance','pvBonus','pvSubtotal','pvLate','pvDeduction','pvTotal']
        .forEach(id => {
            document.getElementById(id).textContent = '-';
        });
    document.getElementById('pvLateLabel').textContent = 'Potongan Terlambat (0x)';
}

// Init jika ada old value (setelah validation error)
document.addEventListener('DOMContentLoaded', function () {
    const empSelect = document.getElementById('employeeSelect');
    if (empSelect.value) {
        onEmployeeChange();
    }
});
</script>
@endpush