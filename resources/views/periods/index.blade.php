@extends('layouts.app')

@section('title', 'Periode Penggajian')
@section('page-title', 'Periode Penggajian')
@section('page-subtitle', 'Kelola periode penggajian bulanan')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama periode..."
                    value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="year" class="form-control" placeholder="Tahun"
                    value="{{ $filters['year'] ?? '' }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('periods.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Periode
    </a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Periode</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Catatan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periods as $p)
                <tr>
                    <td>{{ $p['name'] }}</td>
                    <td>{{ $bulanIndo[$p['month']] ?? $p['month'] }}</td>
                    <td>{{ $p['year'] }}</td>
                    <td>{{ $p['notes'] ?? '-' }}</td>
                    <td class="text-end">
                        <a href="{{ route('periods.edit', $p['id']) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('periods.destroy', $p['id']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada data periode</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection