@extends('layouts.app')

@section('title', 'Edit Slip Gaji')
@section('page-title', 'Edit Slip Gaji')
@section('page-subtitle', 'Perbarui data slip gaji')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Edit Slip: {{ $slip['employee']['full_name'] ?? '-' }}</h1>
        <p>{{ $slip['period']['period_name'] ?? '-' }}</p>
    </div>
</div>

@if($slip['status'] === 'sent')
<div class="alert-custom alert-warning mb-4">
    <i class="bi bi-lock-fill"></i>
    Slip yang sudah terkirim tidak dapat diedit.
</div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pencil-fill"></i>
                    Form Edit Slip Gaji
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('salary-slips.update', $slip['id']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <fieldset {{ $slip['status'] === 'sent' ? 'disabled' : '' }}>

                        {{-- Info karyawan & periode (readonly) --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Karyawan</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $slip['employee']['full_name'] ?? '-' }}"
                                       disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periode</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $slip['period']['period_name'] ?? '-' }}"
                                       disabled>
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    Kategori Gaji <span class="text-danger">*</span>
                                </label>
                                <select name="category_id"
                                        class="form-select"
                                        id="categorySelect"
                                        onchange="updatePreview()"
                                        required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat['id'] }}"
                                            data-base="{{ $cat['base_salary'] }}"
                                            data-allowance="{{ $cat['allowance'] }}"
                                            data-penalty="{{ $cat['late_penalty'] }}"
                                            {{ old('category_id', $slip['category']['id'] ?? '') == $cat['id'] ? 'selected' : '' }}>
                                        {{ $cat['category_name'] }}
                                        ({{ rupiah($cat['base_salary']) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Hari Masuk <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="total_working_days"
                                           id="workingDays"
                                           class="form-control"
                                           value="{{ old('total_working_days', $slip['total_working_days']) }}"
                                           min="0" max="31" required
                                           oninput="updatePreview()">
                                    <span class="input-group-text">hari</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Keterlambatan</label>
                                <div class="input-group">
                                    <input type="number"
                                           name="late_count"
                                           id="lateCount"
                                           class="form-control"
                                           value="{{ old('late_count', $slip['late_count']) }}"
                                           min="0"
                                           oninput="updatePreview()">
                                    <span class="input-group-text">kali</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number"
                                           name="bonus"
                                           id="bonus"
                                           class="form-control"
                                           value="{{ old('bonus', $slip['bonus']) }}"
                                           min="0" step="1000"
                                           oninput="updatePreview()">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Potongan Tambahan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number"
                                           name="additional_deduction"
                                           id="additionalDeduction"
                                           class="form-control"
                                           value="{{ old('additional_deduction', $slip['additional_deduction']) }}"
                                           min="0" step="1000"
                                           oninput="updatePreview()">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes"
                                          class="form-control"
                                          rows="2">{{ old('notes', $slip['notes']) }}</textarea>
                            </div>

                        </div>

                        @if($slip['status'] !== 'sent')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-600">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('salary-slips.show', $slip['id']) }}"
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

    {{-- Preview --}}
    <div class="col-md-5">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-calculator-fill"></i>
                    Preview Kalkulasi
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
                        <td class="py-2 text-muted" id="pvLateLabel">Potongan Terlambat</td>
                        <td class="py-2 text-end fw-600 text-danger" id="pvLate">-</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-muted">Potongan Tambahan</td>
                        <td class="py-2 text-end fw-600 text-danger" id="pvDeduction">-</td>
                    </tr>
                    <tr style="border-top:2px solid var(--primary);background:#FFF3CD;">
                        <td class="py-3 fw-700" style="padding-left:8px;">TOTAL</td>
                        <td class="py-3 text-end fw-700 text-success"
                            id="pvTotal"
                            style="font-size:18px;padding-right:8px;">
                            -
                        </td>
                    </tr>
                </table>
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

function getSelectedCategory() {
    const select = document.getElementById('categorySelect');
    const option = select.options[select.selectedIndex];
    if (!select.value) return null;
    return {
        base:      parseFloat(option.dataset.base)      || 0,
        allowance: parseFloat(option.dataset.allowance) || 0,
        penalty:   parseFloat(option.dataset.penalty)   || 0,
    };
}

function updatePreview() {
    const cat       = getSelectedCategory();
    const lateCount = parseInt(document.getElementById('lateCount').value) || 0;
    const bonus     = parseFloat(document.getElementById('bonus').value) || 0;
    const deduction = parseFloat(document.getElementById('additionalDeduction').value) || 0;

    if (!cat) return;

    const latePenalty = lateCount * cat.penalty;
    const subtotal    = cat.base + cat.allowance + bonus;
    const total       = Math.max(0, subtotal - latePenalty - deduction);

    document.getElementById('pvBase').textContent      = formatRp(cat.base);
    document.getElementById('pvAllowance').textContent = formatRp(cat.allowance);
    document.getElementById('pvBonus').textContent     = formatRp(bonus);
    document.getElementById('pvSubtotal').textContent  = formatRp(subtotal);
    document.getElementById('pvLateLabel').textContent = 'Potongan Terlambat (' + lateCount + 'x)';
    document.getElementById('pvLate').textContent      = '- ' + formatRp(latePenalty);
    document.getElementById('pvDeduction').textContent = '- ' + formatRp(deduction);
    document.getElementById('pvTotal').textContent     = formatRp(total);
}

document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush