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
                <div class="col-md-3"><label class="form-label">Hari Kerja</label><input type="number" min="0" name="hari_kerja" class="form-control" value="{{ old('hari_kerja', $slip['hari_kerja']) }}" required></div>
                <div class="col-md-3"><label class="form-label">Alfa</label><input type="number" min="0" name="alfa" class="form-control" value="{{ old('alfa', $slip['alfa']) }}"></div>
                <div class="col-md-3"><label class="form-label">Izin</label><input type="number" min="0" name="izin" class="form-control" value="{{ old('izin', $slip['izin']) }}"></div>
                <div class="col-md-3"><label class="form-label">Sakit</label><input type="number" min="0" name="sakit" class="form-control" value="{{ old('sakit', $slip['sakit']) }}"></div>
                <div class="col-md-3"><label class="form-label">Off</label><input type="number" min="0" name="off" class="form-control" value="{{ old('off', $slip['off']) }}"></div>
                <div class="col-md-3"><label class="form-label">Lembur (Rp)</label><input type="number" min="0" name="lembur" class="form-control" value="{{ old('lembur', $slip['lembur']) }}"></div>
                <div class="col-md-3"><label class="form-label">Telat</label><input type="number" min="0" name="telat" class="form-control" value="{{ old('telat', $slip['telat']) }}"></div>
                <div class="col-md-3"><label class="form-label">Harian (Rp)</label><input type="number" min="0" name="harian" class="form-control" value="{{ old('harian', $slip['harian']) }}"></div>
                <div class="col-md-3"><label class="form-label">Tunjangan Jabatan</label><input type="number" min="0" name="tunjangan_jabatan" class="form-control" value="{{ old('tunjangan_jabatan', $slip['tunjangan_jabatan']) }}"></div>
                <div class="col-md-3"><label class="form-label">BPJS</label><input type="number" min="0" name="tunjangan_bpjs" class="form-control" value="{{ old('tunjangan_bpjs', $slip['tunjangan_bpjs']) }}"></div>
                <div class="col-md-3"><label class="form-label">Bonus Omset</label><input type="number" min="0" name="bonus_omset" class="form-control" value="{{ old('bonus_omset', $slip['bonus_omset']) }}"></div>
                <div class="col-md-3"><label class="form-label">Bonus Kinerja</label><input type="number" min="0" name="bonus_kinerja" class="form-control" value="{{ old('bonus_kinerja', $slip['bonus_kinerja']) }}"></div>
                <div class="col-md-3"><label class="form-label">Cashbond</label><input type="number" min="0" name="cashbond" class="form-control" value="{{ old('cashbond', $slip['cashbond']) }}"></div>
                <div class="col-md-3"><label class="form-label">Tabungan</label><input type="number" min="0" name="tabungan" class="form-control" value="{{ old('tabungan', $slip['tabungan']) }}"></div>
            </div>
            <div class="form-text mt-2">Masuk, Gaji Pokok, Tunjangan Transport, Tunjangan Masa Kerja, Bonus Disiplin, THP, dan Total akan dihitung ulang otomatis sesuai pengaturan Kategorikal setelah disimpan.</div>
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