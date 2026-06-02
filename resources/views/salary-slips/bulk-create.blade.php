@extends('layouts.app')

@section('title', 'Input Massal Slip Gaji')
@section('page-title', 'Input Massal Slip Gaji')
@section('page-subtitle', 'Generate slip gaji untuk banyak karyawan sekaligus')

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
                <li class="breadcrumb-item active">Input Massal</li>
            </ol>
        </nav>
        <h1>Input Massal Slip Gaji</h1>
        <p>Generate slip gaji untuk beberapa karyawan sekaligus</p>
    </div>
    <a href="{{ route('salary-slips.create') }}" class="btn btn-outline-secondary fw-600">
        <i class="bi bi-person me-2"></i>Beralih ke Input Single
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-people-fill"></i>
            Form Input Massal
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
            Tidak ada periode aktif (open). Buka periode terlebih dahulu.
        </div>
        @endif

        <form action="{{ route('salary-slips.bulk-generate') }}" method="POST" id="bulkForm">
            @csrf

            {{-- Pilih Periode --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-600">
                        Periode Penggajian <span class="text-danger">*</span>
                    </label>
                    <select name="period_id"
                            class="form-select @error('period_id') is-invalid @enderror"
                            required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periods as $period)
                        <option value="{{ $period['id'] }}"
                                {{ (old('period_id', $selectedPeriod) == $period['id']) ? 'selected' : '' }}>
                            {{ $period['period_name'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('period_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-outline-secondary fw-600"
                            onclick="addAllEmployees()">
                        <i class="bi bi-people-fill me-2"></i>
                        Tambah Semua Karyawan ({{ count($employees) }})
                    </button>
                </div>
            </div>

            {{-- Tabel Input Massal --}}
            <div style="overflow-x:auto;">
                <table class="table-custom w-100" id="bulkTable">
                    <thead>
                        <tr>
                            <th width="25%">Karyawan</th>
                            <th width="20%">Kategori Gaji</th>
                            <th width="10%">Hari Masuk</th>
                            <th width="10%">Terlambat</th>
                            <th width="12%">Bonus (Rp)</th>
                            <th width="12%">Potongan (Rp)</th>
                            <th width="8%">Est. Gaji</th>
                            <th width="3%"></th>
                        </tr>
                    </thead>
                    <tbody id="bulkBody">
                        <tr id="emptyRow">
                            <td colspan="8">
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon">👆</div>
                                    <p>Klik "Tambah Karyawan" untuk memulai input massal</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-4 align-items-center">
                <button type="button"
                        class="btn btn-outline-secondary fw-600"
                        onclick="addEmployeeRow()">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Satu Karyawan
                </button>

                <button type="submit"
                        class="btn btn-primary fw-600 ms-auto"
                        id="submitBtn"
                        {{ count($periods) === 0 ? 'disabled' : '' }}>
                    <i class="bi bi-check-lg me-2"></i>
                    Generate <span id="countLabel">0</span> Slip Gaji
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const employees  = @json($employees);
const categories = @json($categories);
let rowCount     = 0;

function formatRp(val) {
    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
}

function getCategoryData(catId) {
    return categories.find(c => c.id == catId) || null;
}

function calcRowTotal(row) {
    const catId     = row.querySelector('.cat-select').value;
    const lateCount = parseInt(row.querySelector('.late-count').value) || 0;
    const bonus     = parseFloat(row.querySelector('.bonus-input').value) || 0;
    const deduction = parseFloat(row.querySelector('.deduction-input').value) || 0;
    const cat       = getCategoryData(catId);

    if (!cat) {
        row.querySelector('.est-salary').textContent = '-';
        return;
    }

    const total = Math.max(0,
        cat.base_salary + cat.allowance + bonus
        - (lateCount * cat.late_penalty)
        - deduction
    );

    row.querySelector('.est-salary').textContent = formatRp(total);
}

function addEmployeeRow(empId = '', catId = '') {
    const tbody = document.getElementById('bulkBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const idx = rowCount++;
    const tr  = document.createElement('tr');
    tr.id     = 'row-' + idx;
    tr.innerHTML = `
        <td>
            <select name="employees[${idx}][employee_id]"
                    class="form-select form-select-sm emp-select"
                    onchange="onEmpChange(this, ${idx})"
                    required>
                <option value="">-- Pilih --</option>
                ${employees.map(e =>
                    `<option value="${e.id}"
                             data-cat-id="${e.category?.id || ''}"
                             ${e.id == empId ? 'selected' : ''}>
                        ${e.full_name}
                    </option>`
                ).join('')}
            </select>
        </td>
        <td>
            <select name="employees[${idx}][category_id]"
                    class="form-select form-select-sm cat-select"
                    onchange="calcRowTotal(document.getElementById('row-${idx}'))"
                    required>
                <option value="">-- Pilih --</option>
                ${categories.map(c =>
                    `<option value="${c.id}"
                             data-base="${c.base_salary}"
                             data-allowance="${c.allowance}"
                             data-penalty="${c.late_penalty}"
                             ${c.id == catId ? 'selected' : ''}>
                        ${c.category_name}
                    </option>`
                ).join('')}
            </select>
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][total_working_days]"
                   class="form-control form-control-sm"
                   value="26" min="0" max="31" required
                   onchange="calcRowTotal(document.getElementById('row-${idx}'))">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][late_count]"
                   class="form-control form-control-sm late-count"
                   value="0" min="0"
                   onchange="calcRowTotal(document.getElementById('row-${idx}'))">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][bonus]"
                   class="form-control form-control-sm bonus-input"
                   value="0" min="0" step="1000"
                   onchange="calcRowTotal(document.getElementById('row-${idx}'))">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][additional_deduction]"
                   class="form-control form-control-sm deduction-input"
                   value="0" min="0" step="1000"
                   onchange="calcRowTotal(document.getElementById('row-${idx}'))">
        </td>
        <td>
            <span class="est-salary fw-600 fs-13 text-success">-</span>
        </td>
        <td>
            <button type="button"
                    class="btn-action delete"
                    onclick="removeRow(${idx})"
                    title="Hapus">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    updateCount();

    // Auto select kategori jika ada
    if (catId) {
        calcRowTotal(tr);
    }
}

function addAllEmployees() {
    employees.forEach(emp => {
        addEmployeeRow(emp.id, emp.category?.id || '');
    });
}

function onEmpChange(select, idx) {
    const option = select.options[select.selectedIndex];
    const catId  = option.dataset.catId;
    const row    = document.getElementById('row-' + idx);

    if (catId) {
        row.querySelector('.cat-select').value = catId;
        calcRowTotal(row);
    }
}

function removeRow(idx) {
    const row = document.getElementById('row-' + idx);
    if (row) row.remove();
    updateCount();

    if (document.querySelectorAll('#bulkBody tr').length === 0) {
        const tbody = document.getElementById('bulkBody');
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="8">
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">👆</div>
                        <p>Klik "Tambah Karyawan" untuk memulai input massal</p>
                    </div>
                </td>
            </tr>`;
    }
}

function updateCount() {
    const count = document.querySelectorAll('#bulkBody tr[id^="row-"]').length;
    document.getElementById('countLabel').textContent = count;
}
</script>
@endpush