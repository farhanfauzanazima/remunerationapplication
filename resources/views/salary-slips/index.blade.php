@extends('layouts.app')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-subtitle', 'Cari dan kelola slip gaji karyawan')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="payroll_period_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $p)
                        <option value="{{ $p['id'] }}" {{ ($filters['payroll_period_id'] ?? '') == $p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="employee_search" class="form-control" placeholder="Cari nama karyawan"
                    value="{{ $filters['employee_search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select name="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ ($filters['branch_id'] ?? '') == $b['id'] ? 'selected' : '' }}>{{ $b['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ ($filters['status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ ($filters['status'] ?? '') == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tenure" class="form-select">
                    <option value="">Semua Masa Kerja</option>
                    <option value="6_months" {{ ($filters['tenure'] ?? '') == '6_months' ? 'selected' : '' }}>≥ 6 Bulan</option>
                    <option value="1_year" {{ ($filters['tenure'] ?? '') == '1_year' ? 'selected' : '' }}>≥ 1 Tahun</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabTetap">Karyawan Tetap ({{ $pagination['tetap']['total'] ?? 0 }})</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPartime">Tim Partime ({{ $pagination['partime']['total'] ?? 0 }})</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tabTetap">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Karyawan</th><th>Cabang</th><th>Periode</th><th>Masuk</th><th>THP</th><th>Total</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($tetap as $slip)
                        <tr>
                            <td>{{ $slip['employee']['name'] ?? '-' }}</td>
                            <td>{{ $slip['employee']['branch']['name'] ?? '-' }}</td>
                            <td>{{ $slip['payroll_period']['name'] ?? '-' }}</td>
                            <td>{{ $slip['masuk'] }}</td>
                            <td>Rp{{ number_format($slip['thp'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($slip['total_gaji'], 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('salary-slips.show', ['type' => 'tetap', 'id' => $slip['id']]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="{{ route('salary-slips.edit', ['type' => 'tetap', 'id' => $slip['id']]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('salary-slips.destroy', ['type' => 'tetap', 'id' => $slip['id']]) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tabPartime">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Karyawan</th><th>Cabang</th><th>Periode</th><th>Total Fee</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($partime as $slip)
                        <tr>
                            <td>{{ $slip['employee']['name'] ?? '-' }}</td>
                            <td>{{ $slip['employee']['branch']['name'] ?? '-' }}</td>
                            <td>{{ $slip['payroll_period']['name'] ?? '-' }}</td>
                            <td>Rp{{ number_format($slip['total_fee'], 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('salary-slips.show', ['type' => 'partime', 'id' => $slip['id']]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="{{ route('salary-slips.edit', ['type' => 'partime', 'id' => $slip['id']]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('salary-slips.destroy', ['type' => 'partime', 'id' => $slip['id']]) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection