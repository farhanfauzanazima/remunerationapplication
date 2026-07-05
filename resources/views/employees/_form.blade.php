<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control"
            value="{{ old('name', $employee['name'] ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Kode/ID Karyawan <span class="text-muted">(opsional)</span></label>
        <input type="text" name="code" class="form-control"
            value="{{ old('code', $employee['code'] ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Jabatan</label>
        <select name="position_id" class="form-select" required>
            <option value="">-- Pilih Jabatan --</option>
            @foreach($positions as $p)
                <option value="{{ $p['id'] }}"
                    {{ old('position_id', $employee['position']['id'] ?? '') == $p['id'] ? 'selected' : '' }}>
                    {{ $p['name'] }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Belum ada jabatannya? <a href="{{ route('positions.index') }}" target="_blank">Tambah jabatan baru</a></div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Cabang</label>
        <select name="branch_id" class="form-select" required>
            <option value="">-- Pilih Cabang --</option>
            @foreach($branches as $b)
                <option value="{{ $b['id'] }}"
                    {{ old('branch_id', $employee['branch']['id'] ?? '') == $b['id'] ? 'selected' : '' }}>
                    {{ $b['name'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Bergabung</label>
        <input type="date" name="join_date" class="form-control"
            value="{{ old('join_date', isset($employee['join_date']) ? \Carbon\Carbon::parse($employee['join_date'])->format('Y-m-d') : '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Jenis Karyawan</label>
        <select name="employee_type" class="form-select" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="tetap" {{ old('employee_type', $employee['employee_type'] ?? '') == 'tetap' ? 'selected' : '' }}>Karyawan Tetap</option>
            <option value="partime" {{ old('employee_type', $employee['employee_type'] ?? '') == 'partime' ? 'selected' : '' }}>Tim Partime</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nomor HP</label>
        <input type="text" name="phone" class="form-control" placeholder="081234567890"
            value="{{ old('phone', $employee['phone'] ?? '') }}">
        <div class="form-text">Gunakan format <strong>08xxx</strong>, contoh: 081234567890 (bukan +62 atau 62xxx)</div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Email <span class="text-muted">(untuk kirim slip gaji)</span></label>
        <input type="email" name="email" class="form-control"
            value="{{ old('email', $employee['email'] ?? '') }}">
    </div>

    @if($employee)
    <div class="col-md-6">
        <label class="form-label">Status Karyawan</label>
        <select name="status" class="form-select" required>
            <option value="aktif" {{ old('status', $employee['status'] ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $employee['status'] ?? '') == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
        </select>
    </div>
    @else
        <input type="hidden" name="status" value="aktif">
    @endif

    <div class="col-12"><hr><h6 class="text-muted">Data Rekening</h6></div>

    <div class="col-md-4">
        <label class="form-label">Nomor Rekening</label>
        <input type="text" name="bank_account_number" class="form-control"
            value="{{ old('bank_account_number', $employee['bank_account_number'] ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Atas Nama Rekening</label>
        <input type="text" name="bank_account_name" class="form-control"
            value="{{ old('bank_account_name', $employee['bank_account_name'] ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Nama Bank</label>
        <input type="text" name="bank_name" class="form-control" placeholder="Contoh: MANDIRI, BCA, DANA"
            value="{{ old('bank_name', $employee['bank_name'] ?? '') }}">
    </div>
</div>
<hr class="my-4">