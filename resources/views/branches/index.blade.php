@extends('layouts.app')

@section('title', 'Cabang')
@section('page-title', 'Manajemen Cabang')
@section('page-subtitle', 'Kelola daftar cabang restoran')

@section('content')
<div class="d-flex justify-content-end mb-3">
    @if(session('user.role') === 'owner')
        <a href="{{ route('branches.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Cabang
        </a>
    @endif
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Cabang</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Jumlah Karyawan</th>
                    @if(session('user.role') === 'owner')
                        <th class="text-end">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td>{{ $branch['name'] }}</td>
                    <td>{{ $branch['address'] ?? '-' }}</td>
                    <td>{{ $branch['phone'] ?? '-' }}</td>
                    <td>{{ $branch['employees_count'] ?? 0 }}</td>
                    @if(session('user.role') === 'owner')
                    <td class="text-end">
                        <a href="{{ route('branches.edit', $branch['id']) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('branches.destroy', $branch['id']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada data cabang</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection