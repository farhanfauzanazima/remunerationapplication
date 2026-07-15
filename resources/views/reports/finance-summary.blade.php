@extends('layouts.app')

@section('title', 'Laporan Slip Gaji untuk Tim Keuangan')
@section('page-title', 'Laporan Slip Gaji untuk Tim Keuangan')
@section('page-subtitle', 'Daftar lengkap seluruh karyawan per cabang, siap unduh PDF')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Periode Penggajian</label>
                <select name="payroll_period_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p['id'] }}" {{ ($filters['payroll_period_id'] ?? '') == $p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cabang</label>
                <select name="branch_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ ($filters['branch_id'] ?? '') == $b['id'] ? 'selected' : '' }}>{{ $b['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($report)
<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="{{ route('reports.finance-summary.preview-pdf', $filters) }}" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-eye"></i> Preview PDF
    </a>
    <a href="{{ route('reports.finance-summary.download-pdf', $filters) }}" class="btn btn-success">
        <i class="bi bi-download"></i> Download PDF
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Karyawan Tetap</div>
        <div class="fs-4 fw-bold">{{ $report['totals']['total_karyawan_tetap'] }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Tim Partime</div>
        <div class="fs-4 fw-bold">{{ $report['totals']['total_karyawan_partime'] }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Total Tabungan</div>
        <div class="fs-6 fw-bold">Rp{{ number_format($report['totals']['total_tabungan'],0,',','.') }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Total Keseluruhan</div>
        <div class="fs-6 fw-bold">Rp{{ number_format($report['totals']['total_keseluruhan'],0,',','.') }}</div>
    </div></div></div>
</div>

<h6 class="mb-2"><i class="bi bi-person-workspace"></i> Karyawan Tetap</h6>
<div class="card mb-4">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm" style="font-size: 12px; white-space: nowrap;">
            <thead class="table-light">
                <tr>
                    <th>Nama</th><th>Bergabung</th><th>Jabatan</th>
                    <th>Total Shift</th><th>Total Full</th><th>Total Parsial</th><th>Gaji Pokok</th>
                    <th>Jam Lembur</th><th>Total Lembur</th><th>Telat</th>
                    <th>Transport</th><th>T.Jabatan</th><th>BPJS</th><th>T.Masa Kerja</th>
                    <th>B.Disiplin</th><th>B.Omset</th><th>B.Kinerja</th><th>Cashbond</th><th>Tabungan</th>
                    <th>THP</th><th>Total</th><th>No Rek</th><th>Atas Nama</th><th>Bank</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['tetap'] as $slip)
                <tr>
                    <td>{{ $slip['employee']['name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($slip['employee']['join_date'])->format('d-m-Y') }}</td>
                    <td>{{ $slip['employee']['position']['name'] ?? '-' }}</td>
                    <td>{{ number_format($slip['total_shift'],0,',','.') }}</td>
                    <td>{{ number_format($slip['total_full'],0,',','.') }}</td>
                    <td>{{ number_format($slip['total_parsial'],0,',','.') }}</td>
                    <td><strong>{{ number_format($slip['gaji_pokok'],0,',','.') }}</strong></td>
                    <td>{{ $slip['jam_lembur'] }}</td>
                    <td>{{ number_format($slip['lembur'],0,',','.') }}</td>
                    <td>{{ $slip['telat'] }}</td>
                    <td>{{ number_format($slip['tunjangan_transport'],0,',','.') }}</td>
                    <td>{{ number_format($slip['tunjangan_jabatan'],0,',','.') }}</td>
                    <td>{{ number_format($slip['tunjangan_bpjs'],0,',','.') }}</td>
                    <td>{{ number_format($slip['tunjangan_masa_kerja'],0,',','.') }}</td>
                    <td>{{ number_format($slip['bonus_disiplin'],0,',','.') }}</td>
                    <td>{{ number_format($slip['bonus_omset'],0,',','.') }}</td>
                    <td>{{ number_format($slip['bonus_kinerja'],0,',','.') }}</td>
                    <td>{{ number_format($slip['cashbond'],0,',','.') }}</td>
                    <td>{{ number_format($slip['tabungan'],0,',','.') }}</td>
                    <td><strong>{{ number_format($slip['thp'],0,',','.') }}</strong></td>
                    <td><strong>{{ number_format($slip['total_gaji'],0,',','.') }}</strong></td>
                    <td>{{ $slip['employee']['bank_account_number'] ?? '-' }}</td>
                    <td>{{ $slip['employee']['bank_account_name'] ?? '-' }}</td>
                    <td>{{ $slip['employee']['bank_name'] ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="24" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h6 class="mb-2"><i class="bi bi-people"></i> Tim Partime</h6>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm" style="font-size: 12px; white-space: nowrap;">
            <thead class="table-light">
                <tr>
                    <th>Nama</th><th>Bergabung</th><th>Jabatan</th><th>Hari Kerja</th><th>Full</th><th>Shift</th><th>Reguler</th><th>Sakit</th><th>Off</th>
                    <th>Tunjangan</th><th>Total Full</th><th>Total Shift</th><th>Total Reguler</th><th>Total Transport</th><th>Bonus</th><th>Total Fee</th>
                    <th>No Rek</th><th>Atas Nama</th><th>Bank</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['partime'] as $slip)
                <tr>
                    <td>{{ $slip['employee']['name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($slip['employee']['join_date'])->format('d-m-Y') }}</td>
                    <td>{{ $slip['employee']['position']['name'] ?? '-' }}</td>
                    <td>{{ $slip['hari_kerja'] }}</td><td>{{ $slip['full'] }}</td><td>{{ $slip['shift'] }}</td><td>{{ $slip['reguler'] }}</td><td>{{ $slip['sakit'] }}</td><td>{{ $slip['off'] }}</td>
                    <td>{{ number_format($slip['tunjangan'],0,',','.') }}</td><td>{{ number_format($slip['total_full'],0,',','.') }}</td>
                    <td>{{ number_format($slip['total_shift'],0,',','.') }}</td><td>{{ number_format($slip['total_reguler'],0,',','.') }}</td>
                    <td>{{ number_format($slip['total_transport'],0,',','.') }}</td><td>{{ number_format($slip['bonus'],0,',','.') }}</td>
                    <td><strong>{{ number_format($slip['total_fee'],0,',','.') }}</strong></td>
                    <td>{{ $slip['employee']['bank_account_number'] ?? '-' }}</td><td>{{ $slip['employee']['bank_account_name'] ?? '-' }}</td><td>{{ $slip['employee']['bank_name'] ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="19" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">Pilih Periode dan Cabang untuk menampilkan laporan.</div>
@endif
@endsection