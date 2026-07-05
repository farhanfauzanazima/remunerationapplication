@extends('layouts.app')

@section('title', 'Manajemen HR')
@section('page-title', 'Manajemen HR')
@section('page-subtitle', 'Kelola akun HR dan hak akses cabang')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahHr">
        <i class="bi bi-plus-lg"></i> Tambah Akun HR
    </button>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username (Email Login)</th>
                    <th>Akses Cabang</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hrUsers as $hr)
                <tr>
                    <td>{{ $hr['name'] }}</td>
                    <td><code>{{ $hr['email'] }}</code></td>
                    <td>
                        @if($hr['has_all_branch_access'])
                            <span class="badge bg-success">Semua Cabang</span>
                        @else
                            @forelse($hr['branches'] ?? [] as $b)
                                <span class="badge bg-secondary">{{ $b['name'] }}</span>
                            @empty
                                <span class="text-muted">Belum ada cabang</span>
                            @endforelse
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#modalEditHr{{ $hr['id'] }}">Edit</button>

                        <form action="{{ route('hr-management.reset-password', $hr['id']) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Reset password akun ini ke default?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning">Reset Password</button>
                        </form>

                        <form action="{{ route('hr-management.destroy', $hr['id']) }}" method="POST" class="d-inline"
                            onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>

                {{-- Modal Edit --}}
                <div class="modal fade" id="modalEditHr{{ $hr['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('hr-management.update', $hr['id']) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Akun HR</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $hr['name'] }}" required>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" name="has_all_branch_access" value="1"
                                            id="allAccess{{ $hr['id'] }}"
                                            onchange="document.getElementById('branchBox{{ $hr['id'] }}').classList.toggle('d-none', this.checked)"
                                            {{ $hr['has_all_branch_access'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allAccess{{ $hr['id'] }}">Akses Semua Cabang</label>
                                    </div>
                                    <div id="branchBox{{ $hr['id'] }}" class="{{ $hr['has_all_branch_access'] ? 'd-none' : '' }}">
                                        <label class="form-label">Pilih Cabang</label>
                                        @foreach($branches as $b)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="branch_ids[]" value="{{ $b['id'] }}"
                                                    {{ collect($hr['branches'] ?? [])->pluck('id')->contains($b['id']) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $b['name'] }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada akun HR</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahHr" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hr-management.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun HR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama HR</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Dedi" required>
                        <div class="form-text">Username login otomatis dibuat: <code>nama@warungsatelanud.id</code>, password default <code>password123</code>.</div>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="has_all_branch_access" value="1"
                            id="allAccessNew" onchange="document.getElementById('branchBoxNew').classList.toggle('d-none', this.checked)">
                        <label class="form-check-label" for="allAccessNew">Akses Semua Cabang</label>
                    </div>
                    <div id="branchBoxNew">
                        <label class="form-label">Pilih Cabang</label>
                        @foreach($branches as $b)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="branch_ids[]" value="{{ $b['id'] }}">
                                <label class="form-check-label">{{ $b['name'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection