@extends('layouts.app')

@section('title', 'Kategorikal')
@section('page-title', 'Kategorikal')
@section('page-subtitle', 'Pengaturan nominal & aturan gaji, berlaku sama untuk semua cabang')

@section('content')
<form action="{{ route('categorical.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- KARYAWAN TETAP --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-person-workspace"></i> Karyawan Tetap
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Transport / Hari Masuk (Rp)</label>
                        <input type="number" min="0" name="transport_tetap" class="form-control"
                            value="{{ old('transport_tetap', $setting['transport_tetap'] ?? 0) }}" required>
                        <div class="form-text">Dihitung otomatis: nominal ini × jumlah hari masuk karyawan.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bonus Disiplin / Hari Masuk (Rp)</label>
                        <input type="number" min="0" name="disiplin_bonus_tetap" class="form-control"
                            value="{{ old('disiplin_bonus_tetap', $setting['disiplin_bonus_tetap'] ?? 0) }}" required>
                        <div class="form-text">Dihitung otomatis: nominal ini × jumlah hari masuk karyawan.</div>
                    </div>

                    <hr>

                    <label class="form-label fw-semibold">Tunjangan Masa Kerja</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">Minimal Masa Kerja (bulan)</label>
                            <input type="number" min="0" name="tenure_months_threshold" class="form-control"
                                value="{{ old('tenure_months_threshold', $setting['tenure_months_threshold'] ?? 6) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Nominal Tunjangan (Rp)</label>
                            <input type="number" min="0" name="tenure_bonus_amount" class="form-control"
                                value="{{ old('tenure_bonus_amount', $setting['tenure_bonus_amount'] ?? 0) }}" required>
                        </div>
                    </div>
                    <div class="form-text">
                        Contoh: 6 bulan & Rp100.000 berarti karyawan dengan masa kerja ≥ 6 bulan
                        otomatis mendapat tunjangan masa kerja Rp100.000, kurang dari itu Rp0.
                    </div>
                </div>
            </div>
        </div>

        {{-- TIM PARTIME --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-people"></i> Tim Partime
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Transport / Hari Kerja (Rp)</label>
                        <input type="number" min="0" name="transport_partime" class="form-control"
                            value="{{ old('transport_partime', $setting['transport_partime'] ?? 0) }}" required>
                        <div class="form-text">Dihitung otomatis: nominal ini × (Full + Shift + Reguler).</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label small text-muted">Rate Full (Rp)</label>
                            <input type="number" min="0" name="rate_full" class="form-control"
                                value="{{ old('rate_full', $setting['rate_full'] ?? 0) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted">Rate Shift (Rp)</label>
                            <input type="number" min="0" name="rate_shift" class="form-control"
                                value="{{ old('rate_shift', $setting['rate_shift'] ?? 0) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted">Rate Reguler (Rp)</label>
                            <input type="number" min="0" name="rate_reguler" class="form-control"
                                value="{{ old('rate_reguler', $setting['rate_reguler'] ?? 0) }}" required>
                        </div>
                    </div>
                    <div class="form-text">Masing-masing rate dikali jumlah hari kehadiran pada kategori tersebut.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Simpan Pengaturan
        </button>
        <span class="text-muted small ms-2">Perubahan langsung berlaku untuk seluruh cabang & karyawan.</span>
    </div>
</form>
@endsection