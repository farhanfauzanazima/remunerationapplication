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
    <a href="{{ route('salary-slips.create') }}"
       class="btn btn-outline-secondary fw-600">
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
            Tidak ada periode aktif (open). Minta Kepala Toko untuk membuka periode.
        </div>
        @endif

        <form action="{{ route('salary-slips.bulk-generate') }}"
              method="POST"
              id="bulkForm">
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
                <div class="col-md-5 d-flex align-items-end gap-2">
                    <button type="button"
                            class="btn btn-outline-secondary fw-600"
                            onclick="addAllEmployees()">
                        <i class="bi bi-people-fill me-2"></i>
                        Tambah Semua Karyawan ({{ count($employees) }})
                    </button>
                    <button type="button"
                            class="btn btn-outline-danger fw-600"
                            onclick="clearAllRows()">
                        <i class="bi bi-trash me-2"></i>Hapus Semua
                    </button>
                </div>
            </div>

            {{-- Notifikasi --}}
            <div id="notifBox" style="display:none;" class="mb-3"></div>

            {{-- Tabel Input Massal --}}
            <div style="overflow-x:auto;">
                <table class="table-custom w-100" id="bulkTable">
                    <thead>
                        <tr>
                            <th width="22%">Karyawan</th>
                            <th width="20%">Kategori Gaji</th>
                            <th width="11%">Hari Masuk</th>
                            <th width="10%">Terlambat</th>
                            <th width="13%">Bonus (Rp)</th>
                            <th width="13%">Potongan (Rp)</th>
                            <th width="8%">Est. Gaji</th>
                            <th width="3%"></th>
                        </tr>
                    </thead>
                    <tbody id="bulkBody">
                        <tr id="emptyRow">
                            <td colspan="8">
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon">👆</div>
                                    <p>Klik "Tambah Semua Karyawan" atau pilih karyawan satu per satu</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-4 align-items-center">
                <button type="button"
                        class="btn btn-outline-secondary fw-600"
                        onclick="showAddOneDropdown()">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Satu Karyawan
                </button>

                {{-- Dropdown pilih karyawan satu-satu --}}
                <div id="addOneBox" style="display:none;">
                    <select id="addOneSelect" class="form-select" style="min-width:220px;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp['id'] }}"
                                data-name="{{ $emp['full_name'] }}"
                                data-code="{{ $emp['employee_code'] ?? '-' }}"
                                data-cat-id="{{ $emp['category']['id'] ?? '' }}"
                                data-cat-name="{{ $emp['category']['category_name'] ?? '-' }}"
                                data-base="{{ $emp['category']['base_salary'] ?? 0 }}"
                                data-allowance="{{ $emp['category']['allowance'] ?? 0 }}"
                                data-penalty="{{ $emp['category']['late_penalty'] ?? 0 }}">
                            {{ $emp['full_name'] }}
                            ({{ $emp['employee_code'] ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="button"
                        id="addOneBtn"
                        class="btn btn-secondary fw-600"
                        onclick="addOneEmployee()"
                        style="display:none;">
                    <i class="bi bi-plus"></i> Tambah
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
// Data karyawan dari PHP — digunakan JS
const employees = @json($employees);

// Track ID karyawan yang sudah ada di tabel
let addedEmployeeIds = new Set();
let rowCount = 0;

function formatRp(val) {
    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
}

// ─── Tampilkan/sembunyikan dropdown tambah satu ───────────
function showAddOneDropdown() {
    const box = document.getElementById('addOneBox');
    const btn = document.getElementById('addOneBtn');
    const isVisible = box.style.display !== 'none';

    box.style.display = isVisible ? 'none' : 'block';
    btn.style.display = isVisible ? 'none' : 'inline-block';
}

// ─── Tambah satu karyawan dari dropdown ──────────────────
function addOneEmployee() {
    const select = document.getElementById('addOneSelect');
    const empId  = select.value;

    if (!empId) {
        showNotif('warning', 'Pilih karyawan terlebih dahulu.');
        return;
    }

    // Cek duplikat
    if (addedEmployeeIds.has(empId)) {
        const opt  = select.options[select.selectedIndex];
        showNotif('warning', '"' + opt.dataset.name + '" sudah ada di daftar.');
        return;
    }

    const option = select.options[select.selectedIndex];
    addRow(
        empId,
        option.dataset.name,
        option.dataset.code,
        option.dataset.catId,
        option.dataset.catName,
        parseFloat(option.dataset.base)      || 0,
        parseFloat(option.dataset.allowance) || 0,
        parseFloat(option.dataset.penalty)   || 0
    );

    // Reset dropdown
    select.value = '';
    showNotif('success', '"' + option.dataset.name + '" berhasil ditambahkan.');
}

// ─── Tambah semua karyawan ────────────────────────────────
function addAllEmployees() {
    const notAdded = employees.filter(e => !addedEmployeeIds.has(String(e.id)));

    if (notAdded.length === 0) {
        showNotif('warning', 'Semua karyawan sudah ada di daftar pengisian slip gaji.');
        return;
    }

    notAdded.forEach(emp => {
        addRow(
            String(emp.id),
            emp.full_name,
            emp.employee_code || '-',
            emp.category?.id   || '',
            emp.category?.category_name || '-',
            parseFloat(emp.category?.base_salary)  || 0,
            parseFloat(emp.category?.allowance)    || 0,
            parseFloat(emp.category?.late_penalty) || 0
        );
    });

    showNotif('success', notAdded.length + ' karyawan berhasil ditambahkan ke daftar.');
}

// ─── Tambah baris ke tabel ───────────────────────────────
function addRow(empId, empName, empCode, catId, catName, base, allowance, penalty) {
    const tbody    = document.getElementById('bulkBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const idx = rowCount++;
    const tr  = document.createElement('tr');
    tr.id     = 'row-' + idx;
    tr.dataset.empId = empId;

    tr.innerHTML = `
        {{-- Hidden inputs --}}
        <input type="hidden" name="employees[${idx}][employee_id]" value="${empId}">
        <input type="hidden" name="employees[${idx}][category_id]" value="${catId}">

        <td>
            <div class="fw-700 fs-13">${empName}</div>
            <div class="fs-13 text-muted">${empCode}</div>
        </td>
        <td>
            <div class="fs-13 fw-600">${catName}</div>
            <div class="fs-13 text-muted">${formatRp(base)}/bln</div>
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][total_working_days]"
                   class="form-control form-control-sm"
                   value="26" min="0" max="31" required
                   data-base="${base}" data-allowance="${allowance}" data-penalty="${penalty}"
                   onchange="calcRowTotal(this)">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][late_count]"
                   class="form-control form-control-sm late-count"
                   value="0" min="0"
                   data-base="${base}" data-allowance="${allowance}" data-penalty="${penalty}"
                   onchange="calcRowTotal(this)">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][bonus]"
                   class="form-control form-control-sm bonus-input"
                   value="0" min="0" step="1000"
                   data-base="${base}" data-allowance="${allowance}" data-penalty="${penalty}"
                   onchange="calcRowTotal(this)">
        </td>
        <td>
            <input type="number"
                   name="employees[${idx}][additional_deduction]"
                   class="form-control form-control-sm deduction-input"
                   value="0" min="0" step="1000"
                   data-base="${base}" data-allowance="${allowance}" data-penalty="${penalty}"
                   onchange="calcRowTotal(this)">
        </td>
        <td>
            <span class="est-salary fw-600 fs-13 text-success">
                ${formatRp(base + allowance)}
            </span>
        </td>
        <td>
            <button type="button"
                    class="btn-action delete"
                    onclick="removeRow(${idx}, '${empId}')"
                    title="Hapus">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);

    // Tandai karyawan sudah ditambahkan
    addedEmployeeIds.add(empId);
    updateCount();
}

// ─── Hitung estimasi gaji per baris ──────────────────────
function calcRowTotal(input) {
    const row       = input.closest('tr');
    const lateInput = row.querySelector('.late-count');
    const bonusInput = row.querySelector('.bonus-input');
    const deductInput = row.querySelector('.deduction-input');

    // Ambil data dari input manapun yang diubah
    const base      = parseFloat(input.dataset.base)      || 0;
    const allowance = parseFloat(input.dataset.allowance) || 0;
    const penalty   = parseFloat(input.dataset.penalty)   || 0;

    const lateCount = parseInt(lateInput?.value)    || 0;
    const bonus     = parseFloat(bonusInput?.value) || 0;
    const deduction = parseFloat(deductInput?.value)|| 0;

    const total = Math.max(0,
        base + allowance + bonus - (lateCount * penalty) - deduction
    );

    row.querySelector('.est-salary').textContent = formatRp(total);
}

// ─── Hapus baris ──────────────────────────────────────────
function removeRow(idx, empId) {
    const row = document.getElementById('row-' + idx);
    if (row) {
        row.remove();
        addedEmployeeIds.delete(empId);
        updateCount();
    }

    // Tampilkan empty row jika tidak ada baris
    if (document.querySelectorAll('#bulkBody tr[id^="row-"]').length === 0) {
        const tbody = document.getElementById('bulkBody');
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="8">
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">👆</div>
                        <p>Klik "Tambah Semua Karyawan" atau pilih karyawan satu per satu</p>
                    </div>
                </td>
            </tr>`;
    }
}

// ─── Hapus semua baris ────────────────────────────────────
function clearAllRows() {
    if (!confirm('Hapus semua karyawan dari daftar?')) return;

    addedEmployeeIds.clear();
    rowCount = 0;

    const tbody = document.getElementById('bulkBody');
    tbody.innerHTML = `
        <tr id="emptyRow">
            <td colspan="8">
                <div class="empty-state py-4">
                    <div class="empty-state-icon">👆</div>
                    <p>Klik "Tambah Semua Karyawan" atau pilih karyawan satu per satu</p>
                </div>
            </td>
        </tr>`;
    updateCount();
    showNotif('info', 'Semua karyawan telah dihapus dari daftar.');
}

// ─── Update counter submit button ────────────────────────
function updateCount() {
    const count = document.querySelectorAll('#bulkBody tr[id^="row-"]').length;
    document.getElementById('countLabel').textContent = count;
}

// ─── Tampilkan notifikasi ─────────────────────────────────
function showNotif(type, message) {
    const box      = document.getElementById('notifBox');
    const typeMap  = {
        'success': 'alert-success',
        'warning': 'alert-warning',
        'info':    'alert-info',
        'error':   'alert-error',
    };
    const iconMap  = {
        'success': 'bi-check-circle-fill',
        'warning': 'bi-exclamation-triangle-fill',
        'info':    'bi-info-circle-fill',
        'error':   'bi-exclamation-circle-fill',
    };

    box.className  = 'alert-custom ' + (typeMap[type] || 'alert-info') + ' mb-3';
    box.innerHTML  = `<i class="bi ${iconMap[type] || 'bi-info-circle-fill'}"></i> ${message}`;
    box.style.display = 'flex';

    // Auto hide setelah 4 detik
    clearTimeout(box._timeout);
    box._timeout = setTimeout(() => {
        box.style.display = 'none';
    }, 4000);
}
</script>
@endpush