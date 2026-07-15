@extends('layouts.app')

@section('title', 'Edit Slip Gaji')
@section('page-title', 'Edit Slip Gaji')
@section('page-subtitle', $slip['employee']['name'] ?? '')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('salary-slips.update', ['type' => $type, 'id' => $slip['id']]) }}" method="POST">
            @csrf @method('PUT')

            @if($type === 'tetap')
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Hari Kerja <span class="text-muted small">(catatan)</span></label><input type="number" min="0" name="hari_kerja" class="form-control" value="{{ old('hari_kerja', $slip['hari_kerja']) }}" required></div>
                <div class="col-md-3"><label class="form-label">Alfa</label><input type="number" min="0" name="alfa" class="form-control" value="{{ old('alfa', $slip['alfa']) }}"></div>
                <div class="col-md-3"><label class="form-label">Izin</label><input type="number" min="0" name="izin" class="form-control" value="{{ old('izin', $slip['izin']) }}"></div>
                <div class="col-md-3"><label class="form-label">Sakit</label><input type="number" min="0" name="sakit" class="form-control" value="{{ old('sakit', $slip['sakit']) }}"></div>

                <div class="col-12"><hr><h6 class="text-muted">Gaji Pokok (Shift / Full / Parsial)</h6></div>

                <div class="col-md-2"><label class="form-label">Hari Shift</label><input type="number" min="0" name="hari_shift" class="form-control" value="{{ old('hari_shift', $slip['hari_shift']) }}"></div>
                <div class="col-md-2"><label class="form-label">Nominal Shift</label><input type="number" min="0" name="nominal_shift" class="form-control" value="{{ old('nominal_shift', $slip['nominal_shift']) }}"></div>
                <div class="col-md-2"><label class="form-label">Hari Full</label><input type="number" min="0" name="hari_full" class="form-control" value="{{ old('hari_full', $slip['hari_full']) }}"></div>
                <div class="col-md-2"><label class="form-label">Nominal Full</label><input type="number" min="0" name="nominal_full" class="form-control" value="{{ old('nominal_full', $slip['nominal_full']) }}"></div>
                <div class="col-md-2"><label class="form-label">Hari Parsial</label><input type="number" min="0" name="hari_parsial" class="form-control" value="{{ old('hari_parsial', $slip['hari_parsial']) }}"></div>
                <div class="col-md-2"><label class="form-label">Nominal Parsial</label><input type="number" min="0" name="nominal_parsial" class="form-control" value="{{ old('nominal_parsial', $slip['nominal_parsial']) }}"></div>

                <div class="col-12"><hr></div>

                <div class="col-md-3"><label class="form-label">Jam Lembur <span class="text-muted small">(maks 5)</span></label><input type="number" min="0" max="5" name="jam_lembur" class="form-control" value="{{ old('jam_lembur', $slip['jam_lembur']) }}"></div>
                <div class="col-md-3"><label class="form-label">Telat <span class="text-muted small">(hari)</span></label><input type="number" min="0" name="telat" class="form-control" value="{{ old('telat', $slip['telat']) }}"></div>
                <div class="col-md-3"><label class="form-label">Tunjangan Jabatan</label><input type="number" min="0" name="tunjangan_jabatan" class="form-control" value="{{ old('tunjangan_jabatan', $slip['tunjangan_jabatan']) }}"></div>
                <div class="col-md-3"><label class="form-label">BPJS</label><input type="number" min="0" name="tunjangan_bpjs" class="form-control" value="{{ old('tunjangan_bpjs', $slip['tunjangan_bpjs']) }}"></div>
                <div class="col-md-3"><label class="form-label">Bonus Omset</label><input type="number" min="0" name="bonus_omset" class="form-control" value="{{ old('bonus_omset', $slip['bonus_omset']) }}"></div>
                <div class="col-md-3"><label class="form-label">Bonus Kinerja</label><input type="number" min="0" name="bonus_kinerja" class="form-control" value="{{ old('bonus_kinerja', $slip['bonus_kinerja']) }}"></div>
                <div class="col-md-3"><label class="form-label">Cashbond</label><input type="number" min="0" name="cashbond" class="form-control" value="{{ old('cashbond', $slip['cashbond']) }}"></div>
                <div class="col-md-3"><label class="form-label">Tabungan</label><input type="number" min="0" name="tabungan" class="form-control" value="{{ old('tabungan', $slip['tabungan']) }}"></div>
            </div>
            <div class="form-text mt-2">Masuk, Total Shift/Full/Parsial, Gaji Pokok, Total Lembur, Tunjangan Masa Kerja, Bonus Disiplin, THP, dan Total akan dihitung ulang otomatis setelah disimpan.</div>
            @else
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Hari Kerja</label><input type="number" min="0" name="hari_kerja" class="form-control" value="{{ old('hari_kerja', $slip['hari_kerja']) }}"></div>
                <div class="col-md-4"><label class="form-label">Full</label><input type="number" min="0" name="full" class="form-control" value="{{ old('full', $slip['full']) }}"></div>
                <div class="col-md-4"><label class="form-label">Shift</label><input type="number" min="0" name="shift" class="form-control" value="{{ old('shift', $slip['shift']) }}"></div>
                <div class="col-md-4"><label class="form-label">Reguler</label><input type="number" min="0" name="reguler" class="form-control" value="{{ old('reguler', $slip['reguler']) }}"></div>
                <div class="col-md-4"><label class="form-label">Sakit</label><input type="number" min="0" name="sakit" class="form-control" value="{{ old('sakit', $slip['sakit']) }}"></div>
                <div class="col-md-4"><label class="form-label">Off</label><input type="number" min="0" name="off" class="form-control" value="{{ old('off', $slip['off']) }}"></div>
                <div class="col-md-4"><label class="form-label">Tunjangan</label><input type="number" min="0" name="tunjangan" class="form-control" value="{{ old('tunjangan', $slip['tunjangan']) }}"></div>
                <div class="col-md-4"><label class="form-label">Bonus</label><input type="number" min="0" name="bonus" class="form-control" value="{{ old('bonus', $slip['bonus']) }}"></div>
            </div>
            <div class="form-text mt-2">Total Full, Shift, Reguler, Transport, dan Total Fee akan dihitung ulang otomatis sesuai pengaturan Kategorikal setelah disimpan.</div>
            @endif

            <div class="mt-4">
                <button class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('salary-slips.show', ['type' => $type, 'id' => $slip['id']]) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection