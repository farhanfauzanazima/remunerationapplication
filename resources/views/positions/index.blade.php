@extends('layouts.app')

@section('title', 'Jabatan')
@section('page-title', 'Manajemen Jabatan')
@section('page-subtitle', 'Kelola daftar jabatan karyawan')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJabatan">
        <i class="bi bi-plus-lg"></i> Tambah Jabatan
    </button>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr><th>Nama Jabatan</th><th>Jumlah Karyawan</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($positions as $position)
                <tr>
                    <td>{{ $position['name'] }}</td>
                    <td>{{ $position['employees_count'] ?? 0 }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#modalEdit{{ $position['id'] }}">Edit</button>
                        <form action="{{ route('positions.destroy', $position['id']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit{{ $position['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('positions.update', $position['id']) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title">Edit Jabatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" name="name" class="form-control" value="{{ $position['name'] }}" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="3" class="text-center text-muted">Belum ada data jabatan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambahJabatan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('positions.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control" placeholder="Nama jabatan" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection