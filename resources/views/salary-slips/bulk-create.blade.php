@extends('layouts.app')

@section('title', 'Input Massal Slip Gaji')
@section('page-title', 'Input Massal Slip Gaji')
@section('page-subtitle', 'Pilih periode dan cabang, lalu isi data kehadiran karyawan')

@push('styles')
<style>
    .table-bulk {
        font-size: 13px;
        table-layout: fixed;
        width: 100%;
    }
    .table-bulk th,
    .table-bulk td {
        padding: 6px 8px;
        vertical-align: middle;
        text-align: center;
        width: 115px;
        overflow-wrap: break-word;
    }
    .table-bulk th {
        text-align: center;
        vertical-align: middle;
        white-space: normal;
    }
    /* Kolom nama & jabatan boleh sedikit lebih lebar karena isinya teks panjang */
    .table-bulk th.col-nama,
    .table-bulk td.col-nama,
    .table-bulk th.col-jabatan,
    .table-bulk td.col-jabatan {
        width: 150px;
        text-align: left;
    }
    .table-bulk th.col-aksi,
    .table-bulk td.col-aksi {
        width: 60px;
    }
    .table-bulk input {
        width: 100%;
        text-align: right;
    }
    .table-bulk .locked {
        background: #f1f3f5;
        text-align: right;
        font-weight: 500;
    }
    .table-bulk-wrapper {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Periode Penggajian</label>
                <select name="payroll_period_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p['id'] }}" {{ $selectedPeriod == $p['id'] ? 'selected' : '' }}>
                            {{ $p['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cabang</label>
                <select name="branch_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ $selectedBranch == $b['id'] ? 'selected' : '' }}>
                            {{ $b['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($bulkData)
<form action="{{ route('salary-slips.bulk-generate') }}" method="POST" id="bulkForm">
    @csrf
    <input type="hidden" name="payroll_period_id" value="{{ $selectedPeriod }}">
    <input type="hidden" name="branch_id" value="{{ $selectedBranch }}">

    {{-- ============== KARYAWAN TETAP ============== --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-person-workspace"></i> Karyawan Tetap</span>
            <div>
                <select id="selectTetap" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="">-- Tambah Satu Karyawan --</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOneTetap()">Tambah</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="addAllTetap()">Tambah Semua</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllTetap()">Kosongkan</button>
            </div>
        </div>
        <div class="card-body table-bulk-wrapper">
            <table class="table table-bordered table-bulk align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="col-nama">Nama</th>
                        <th>Bergabung</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th>Hari Kerja</th>
                        <th>Alfa</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Off</th>
                        <th>Masuk</th>
                        <th>Lembur</th>
                        <th>Telat</th>
                        <th>Harian</th>
                        <th>Gaji Pokok</th>
                        <th>Transport</th>
                        <th>T. Jabatan</th>
                        <th>BPJS</th>
                        <th>T. Masa Kerja</th>
                        <th>B. Disiplin</th>
                        <th>B. Omset</th>
                        <th>B. Kinerja</th>
                        <th>Cashbond</th>
                        <th>Tabungan</th>
                        <th>THP</th>
                        <th>Total</th>
                        <th>No Rek</th>
                        <th>Bank</th>
                        <th class="col-aksi"></th>
                    </tr>
                </thead>
                <tbody id="tetapBody"></tbody>
            </table>
        </div>
    </div>

    {{-- ============== TIM PARTIME ============== --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-people"></i> Tim Partime</span>
            <div>
                <select id="selectPartime" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="">-- Tambah Satu Karyawan --</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOnePartime()">Tambah</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="addAllPartime()">Tambah Semua</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllPartime()">Kosongkan</button>
            </div>
        </div>
        <div class="card-body table-bulk-wrapper">
            <table class="table table-bordered table-bulk align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="col-nama">Nama</th>
                        <th>Bergabung</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th>Hari Kerja</th>
                        <th>Full</th>
                        <th>Shift</th>
                        <th>Reguler</th>
                        <th>Sakit</th>
                        <th>Off</th>
                        <th>Tunjangan</th>
                        <th>Total Full</th>
                        <th>Total Shift</th>
                        <th>Total Reguler</th>
                        <th>Total Transport</th>
                        <th>Bonus</th>
                        <th>Total Fee</th>
                        <th>No Rek</th>
                        <th>Bank</th>
                        <th class="col-aksi"></th>
                    </tr>
                </thead>
                <tbody id="partimeBody"></tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-save"></i> Simpan Semua Slip Gaji
    </button>
</form>
@else
<div class="alert alert-info">Silakan pilih Periode Penggajian dan Cabang terlebih dahulu.</div>
@endif

@endsection

@push('scripts')
<script>
const SETTING = @json($bulkData['setting'] ?? []);
const EMPLOYEES_TETAP = @json($bulkData['employees_tetap'] ?? []);
const EMPLOYEES_PARTIME = @json($bulkData['employees_partime'] ?? []);

const addedTetapIds = new Set();
const addedPartimeIds = new Set();

function rupiah(n) {
    n = Number(n) || 0;
    return 'Rp' + n.toLocaleString('id-ID');
}

/* ============ Format Ribuan (titik) untuk input rupiah ============ */

function formatRibuan(value) {
    value = String(value).replace(/\D/g, '');
    if (!value) return '';
    return parseInt(value, 10).toLocaleString('id-ID');
}

function unformatRibuan(value) {
    return parseInt(String(value || '0').replace(/\./g, ''), 10) || 0;
}

// Delegasi event: berlaku juga untuk baris yang ditambahkan setelah halaman dimuat
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('rupiah-input')) {
        const cursorFromEnd = e.target.value.length - e.target.selectionStart;
        e.target.value = formatRibuan(e.target.value);
        const newPos = e.target.value.length - cursorFromEnd;
        e.target.setSelectionRange(newPos, newPos);
    }
});

// Kembalikan ke angka polos sesaat sebelum form dikirim ke server
document.getElementById('bulkForm')?.addEventListener('submit', function () {
    document.querySelectorAll('.rupiah-input').forEach(el => {
        el.value = unformatRibuan(el.value);
    });
});

function readVal(row, name, isRupiah) {
    const el = row.querySelector(`[name$="[${name}]"]`) || row.querySelector(`[name*="[${name}]"]`);
    if (!el) return 0;
    return isRupiah ? unformatRibuan(el.value) : (parseFloat(el.value) || 0);
}

function tenureMonths(joinDate) {
    const start = new Date(joinDate);
    const now = new Date();
    return (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());
}

/* ============ KARYAWAN TETAP ============ */

function populateSelectTetap() {
    const select = document.getElementById('selectTetap');
    select.innerHTML = '<option value="">-- Tambah Satu Karyawan --</option>';
    EMPLOYEES_TETAP.forEach(emp => {
        if (!addedTetapIds.has(emp.id)) {
            select.innerHTML += `<option value="${emp.id}">${emp.name}</option>`;
        }
    });
}

function addAllTetap() {
    EMPLOYEES_TETAP.forEach(emp => { if (!addedTetapIds.has(emp.id)) addRowTetap(emp); });
    populateSelectTetap();
}

function addOneTetap() {
    const id = parseInt(document.getElementById('selectTetap').value);
    const emp = EMPLOYEES_TETAP.find(e => e.id === id);
    if (emp && !addedTetapIds.has(emp.id)) {
        addRowTetap(emp);
        populateSelectTetap();
    }
}

function clearAllTetap() {
    document.getElementById('tetapBody').innerHTML = '';
    addedTetapIds.clear();
    populateSelectTetap();
}

function addRowTetap(emp) {
    addedTetapIds.add(emp.id);
    const existing = emp.slip_tetap_for_period || {};
    const idx = emp.id;
    const tr = document.createElement('tr');
    tr.id = `tetap-row-${idx}`;
    tr.dataset.joinDate = emp.join_date;
    tr.innerHTML = `
        <td class="col-nama">${emp.name}<input type="hidden" name="tetap[${idx}][employee_id]" value="${emp.id}"></td>
        <td>${emp.join_date ? emp.join_date.substring(0,10) : '-'}</td>
        <td class="col-jabatan">${emp.position?.name ?? '-'}</td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][hari_kerja]" value="${existing.hari_kerja ?? 0}" onchange="calcTetap(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][alfa]" value="${existing.alfa ?? 0}" onchange="calcTetap(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][izin]" value="${existing.izin ?? 0}" onchange="calcTetap(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][sakit]" value="${existing.sakit ?? 0}" onchange="calcTetap(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][off]" value="${existing.off ?? 0}" onchange="calcTetap(${idx})"></td>
        <td class="locked" id="tetap-${idx}-masuk">0</td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][lembur]" value="${formatRibuan(existing.lembur ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="tetap[${idx}][telat]" value="${existing.telat ?? 0}"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][harian]" value="${formatRibuan(existing.harian ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td class="locked" id="tetap-${idx}-gaji_pokok">Rp0</td>
        <td class="locked" id="tetap-${idx}-transport">Rp0</td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][tunjangan_jabatan]" value="${formatRibuan(existing.tunjangan_jabatan ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][tunjangan_bpjs]" value="${formatRibuan(existing.tunjangan_bpjs ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td class="locked" id="tetap-${idx}-masa_kerja">Rp0</td>
        <td class="locked" id="tetap-${idx}-disiplin">Rp0</td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][bonus_omset]" value="${formatRibuan(existing.bonus_omset ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][bonus_kinerja]" value="${formatRibuan(existing.bonus_kinerja ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][cashbond]" value="${formatRibuan(existing.cashbond ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="tetap[${idx}][tabungan]" value="${formatRibuan(existing.tabungan ?? 0)}" onchange="calcTetap(${idx})"></td>
        <td class="locked fw-semibold" id="tetap-${idx}-thp">Rp0</td>
        <td class="locked fw-semibold" id="tetap-${idx}-total">Rp0</td>
        <td>${emp.bank_account_number ?? '-'}</td>
        <td>${emp.bank_name ?? '-'}</td>
        <td class="col-aksi"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRowTetap(${idx})"><i class="bi bi-x"></i></button></td>
    `;
    document.getElementById('tetapBody').appendChild(tr);
    calcTetap(idx);
}

function removeRowTetap(idx) {
    document.getElementById(`tetap-row-${idx}`)?.remove();
    addedTetapIds.delete(idx);
    populateSelectTetap();
}

function calcTetap(idx) {
    const row = document.getElementById(`tetap-row-${idx}`);
    if (!row) return;

    const val = (name, isRupiah = false) => {
        const el = row.querySelector(`[name="tetap[${idx}][${name}]"]`);
        if (!el) return 0;
        return isRupiah ? unformatRibuan(el.value) : (parseFloat(el.value) || 0);
    };

    const masuk = Math.max(0, val('hari_kerja') - val('alfa') - val('izin') - val('sakit') - val('off'));
    const gajiPokok = val('harian', true) * masuk;
    const transport = (SETTING.transport_tetap || 0) * masuk;
    const disiplin = (SETTING.disiplin_bonus_tetap || 0) * masuk;
    const months = tenureMonths(row.dataset.joinDate);
    const masaKerja = months >= (SETTING.tenure_months_threshold || 0) ? (SETTING.tenure_bonus_amount || 0) : 0;

    const thp = (val('lembur', true) + gajiPokok + transport + val('tunjangan_jabatan', true) + val('tunjangan_bpjs', true)
        + masaKerja + disiplin + val('bonus_omset', true) + val('bonus_kinerja', true))
        - (val('cashbond', true) + val('tabungan', true));
    const total = thp + val('tabungan', true) + val('cashbond', true);

    document.getElementById(`tetap-${idx}-masuk`).textContent = masuk;
    document.getElementById(`tetap-${idx}-gaji_pokok`).textContent = rupiah(gajiPokok);
    document.getElementById(`tetap-${idx}-transport`).textContent = rupiah(transport);
    document.getElementById(`tetap-${idx}-masa_kerja`).textContent = rupiah(masaKerja);
    document.getElementById(`tetap-${idx}-disiplin`).textContent = rupiah(disiplin);
    document.getElementById(`tetap-${idx}-thp`).textContent = rupiah(thp);
    document.getElementById(`tetap-${idx}-total`).textContent = rupiah(total);
}

/* ============ TIM PARTIME ============ */

function populateSelectPartime() {
    const select = document.getElementById('selectPartime');
    select.innerHTML = '<option value="">-- Tambah Satu Karyawan --</option>';
    EMPLOYEES_PARTIME.forEach(emp => {
        if (!addedPartimeIds.has(emp.id)) {
            select.innerHTML += `<option value="${emp.id}">${emp.name}</option>`;
        }
    });
}

function addAllPartime() {
    EMPLOYEES_PARTIME.forEach(emp => { if (!addedPartimeIds.has(emp.id)) addRowPartime(emp); });
    populateSelectPartime();
}

function addOnePartime() {
    const id = parseInt(document.getElementById('selectPartime').value);
    const emp = EMPLOYEES_PARTIME.find(e => e.id === id);
    if (emp && !addedPartimeIds.has(emp.id)) {
        addRowPartime(emp);
        populateSelectPartime();
    }
}

function clearAllPartime() {
    document.getElementById('partimeBody').innerHTML = '';
    addedPartimeIds.clear();
    populateSelectPartime();
}

function addRowPartime(emp) {
    addedPartimeIds.add(emp.id);
    const existing = emp.slip_partime_for_period || {};
    const idx = emp.id;
    const tr = document.createElement('tr');
    tr.id = `partime-row-${idx}`;
    tr.innerHTML = `
        <td class="col-nama">${emp.name}<input type="hidden" name="partime[${idx}][employee_id]" value="${emp.id}"></td>
        <td>${emp.join_date ? emp.join_date.substring(0,10) : '-'}</td>
        <td class="col-jabatan">${emp.position?.name ?? '-'}</td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][hari_kerja]" value="${existing.hari_kerja ?? 0}"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][full]" value="${existing.full ?? 0}" onchange="calcPartime(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][shift]" value="${existing.shift ?? 0}" onchange="calcPartime(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][reguler]" value="${existing.reguler ?? 0}" onchange="calcPartime(${idx})"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][sakit]" value="${existing.sakit ?? 0}"></td>
        <td><input type="number" min="0" class="form-control form-control-sm" name="partime[${idx}][off]" value="${existing.off ?? 0}"></td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="partime[${idx}][tunjangan]" value="${formatRibuan(existing.tunjangan ?? 0)}" onchange="calcPartime(${idx})"></td>
        <td class="locked" id="partime-${idx}-total_full">Rp0</td>
        <td class="locked" id="partime-${idx}-total_shift">Rp0</td>
        <td class="locked" id="partime-${idx}-total_reguler">Rp0</td>
        <td class="locked" id="partime-${idx}-total_transport">Rp0</td>
        <td><input type="text" inputmode="numeric" class="form-control form-control-sm rupiah-input" name="partime[${idx}][bonus]" value="${formatRibuan(existing.bonus ?? 0)}" onchange="calcPartime(${idx})"></td>
        <td class="locked fw-semibold" id="partime-${idx}-total_fee">Rp0</td>
        <td>${emp.bank_account_number ?? '-'}</td>
        <td>${emp.bank_name ?? '-'}</td>
        <td class="col-aksi"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRowPartime(${idx})"><i class="bi bi-x"></i></button></td>
    `;
    document.getElementById('partimeBody').appendChild(tr);
    calcPartime(idx);
}

function removeRowPartime(idx) {
    document.getElementById(`partime-row-${idx}`)?.remove();
    addedPartimeIds.delete(idx);
    populateSelectPartime();
}

function calcPartime(idx) {
    const row = document.getElementById(`partime-row-${idx}`);
    if (!row) return;

    const val = (name, isRupiah = false) => {
        const el = row.querySelector(`[name="partime[${idx}][${name}]"]`);
        if (!el) return 0;
        return isRupiah ? unformatRibuan(el.value) : (parseFloat(el.value) || 0);
    };

    const totalFull = (SETTING.rate_full || 0) * val('full');
    const totalShift = (SETTING.rate_shift || 0) * val('shift');
    const totalReguler = (SETTING.rate_reguler || 0) * val('reguler');
    const totalTransport = (SETTING.transport_partime || 0) * (val('full') + val('shift') + val('reguler'));
    const totalFee = val('tunjangan', true) + totalFull + totalShift + totalReguler + totalTransport + val('bonus', true);

    document.getElementById(`partime-${idx}-total_full`).textContent = rupiah(totalFull);
    document.getElementById(`partime-${idx}-total_shift`).textContent = rupiah(totalShift);
    document.getElementById(`partime-${idx}-total_reguler`).textContent = rupiah(totalReguler);
    document.getElementById(`partime-${idx}-total_transport`).textContent = rupiah(totalTransport);
    document.getElementById(`partime-${idx}-total_fee`).textContent = rupiah(totalFee);
}

/* Inisialisasi: kalau karyawan sudah punya slip di periode ini, tampilkan otomatis */
document.addEventListener('DOMContentLoaded', () => {
    EMPLOYEES_TETAP.forEach(emp => { if (emp.slip_tetap_for_period) addRowTetap(emp); });
    EMPLOYEES_PARTIME.forEach(emp => { if (emp.slip_partime_for_period) addRowPartime(emp); });
    populateSelectTetap();
    populateSelectPartime();
});
</script>
@endpush