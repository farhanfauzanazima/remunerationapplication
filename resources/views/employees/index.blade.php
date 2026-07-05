@extends('layouts.app')

@section('title', 'Karyawan')
@section('page-title', 'Manajemen Karyawan')
@section('page-subtitle', 'Kelola data karyawan tetap dan tim partime')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..."
                    value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select name="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ ($filters['branch_id'] ?? '') == $b['id'] ? 'selected' : '' }}>
                            {{ $b['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="employee_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="tetap" {{ ($filters['employee_type'] ?? '') == 'tetap' ? 'selected' : '' }}>Tetap</option>
                    <option value="partime" {{ ($filters['employee_type'] ?? '') == 'partime' ? 'selected' : '' }}>Tim Partime</option>
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
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Karyawan
    </a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Jabatan</th>
                    <th>Cabang</th>
                    <th>Jenis</th>
                    <th>Bergabung</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ $emp['code'] ?? '-' }}</td>
                    <td>{{ $emp['position']['name'] ?? '-' }}</td>
                    <td>{{ $emp['branch']['name'] ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $emp['employee_type'] == 'tetap' ? 'bg-primary' : 'bg-info' }}">
                            {{ $emp['employee_type'] == 'tetap' ? 'Tetap' : 'Tim Partime' }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($emp['join_date'])->translatedFormat('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $emp['status'] == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($emp['status']) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('employees.edit', $emp['id']) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('employees.destroy', $emp['id']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">Belum ada data karyawan</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($pagination && $pagination['last_page'] > 1)
        <div class="text-muted small mt-2">
            Halaman {{ $pagination['current_page'] }} dari {{ $pagination['last_page'] }} ({{ $pagination['total'] }} data)
        </div>
        @endif
    </div>
</div>
@endsection