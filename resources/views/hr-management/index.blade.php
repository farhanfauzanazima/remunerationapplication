@extends('layouts.app')

@section('title', 'Manajemen HR')
@section('page-title', 'Manajemen HR')
@section('page-subtitle', 'Kelola akun HR dan Super HR')

@section('content')
@php
    $isOwner = session('user.role') === 'owner';
@endphp

<div class="d-flex justify-content-end gap-2 mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahHr">
        <i class="bi bi-plus-lg"></i> Tambah HR
    </button>
    @if($isOwner)
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambahSuperHr">
            <i class="bi bi-shield-fill-plus"></i> Tambah Super HR
        </button>
    @endif
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username (Email Login)</th>
                    <th>Tipe</th>
                    <th>Akses Cabang</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hrUsers as $hr)
                @php
                    // Aturan tampilan tombol aksi:
                    // - Target Super HR: hanya Owner yang boleh apa-apa
                    // - Target HR biasa: Owner & Super HR sama-sama boleh
                    $canAct = $hr['is_super_hr'] ? $isOwner : true;
                @endphp
                <tr>
                    <td>{{ $hr['name'] }}</td>
                    <td><code>{{ $hr['email'] }}</code></td>
                    <td>
                        @if($hr['is_super_hr'])
                            <span class="badge bg-warning text-dark">Super HR</span>
                        @else
                            <span class="badge bg-secondary">HR</span>
                        @endif
                    </td>
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
                        @if($canAct)
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#modalEditHr{{ $hr['id'] }}">Edit</button>

                            <form action="{{ route('hr-management.reset-password', $hr['id']) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Reset password akun ini ke default?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning">Reset Password</button>
                            </form>

                            @if($isOwner && !$hr['is_super_hr'])
                                <form action="{{ route('hr-management.update', $hr['id']) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Jadikan akun ini Super HR? Akses cabang akan otomatis menjadi semua cabang.')">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="name" value="{{ $hr['name'] }}">
                                    <input type="hidden" name="is_super_hr" value="1">
                                    <button class="btn btn-sm btn-outline-primary">Jadikan Super HR</button>
                                </form>
                            @elseif($isOwner && $hr['is_super_hr'])
                                <form action="{{ route('hr-management.update', $hr['id']) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Turunkan akun ini jadi HR biasa? Akses cabang akan direset, perlu diatur ulang.')">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="name" value="{{ $hr['name'] }}">
                                    <input type="hidden" name="is_super_hr" value="0">
                                    <button class="btn btn-sm btn-outline-secondary">Turunkan ke HR</button>
                                </form>
                            @endif

                            <form action="{{ route('hr-management.destroy', $hr['id']) }}" method="POST" class="d-inline"
                                onsubmit="return confirmDelete(event)">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @else
                            <span class="text-muted small">Hanya Owner yang dapat mengelola</span>
                        @endif
                    </td>
                </tr>

                @if($canAct)
                <div class="modal fade" id="modalEditHr{{ $hr['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('hr-management.update', $hr['id']) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Akun {{ $hr['is_super_hr'] ? 'Super HR' : 'HR' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $hr['name'] }}" required>
                                    </div>

                                    @if(!$hr['is_super_hr'])
                                    {{-- PENTING: hidden input default 0 ini WAJIB ada sebelum checkbox.
                                         Tanpa ini, saat checkbox di-uncheck, field 'has_all_branch_access'
                                         TIDAK ikut terkirim sama sekali (perilaku standar HTML checkbox),
                                         menyebabkan backend tidak pernah menerima perintah update cabang. --}}
                                    <input type="hidden" name="has_all_branch_access" value="0">
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
                                    @else
                                    <div class="text-muted small">Super HR selalu memiliki akses ke semua cabang.</div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada akun HR</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah HR biasa --}}
<div class="modal fade" id="modalTambahHr" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hr-management.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="hr">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun HR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama HR</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Dedi" required>
                        <div class="form-text">Username login otomatis: <code>nama@warungsatelanud.id</code>, password default <code>password123</code>.</div>
                    </div>
                    {{-- Hidden default 0 juga WAJIB di form Tambah, untuk alasan sama seperti form Edit --}}
                    <input type="hidden" name="has_all_branch_access" value="0">
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

{{-- Modal Tambah Super HR — hanya Owner yang melihat tombolnya, TIDAK ADA field cabang sama sekali --}}
@if($isOwner)
<div class="modal fade" id="modalTambahSuperHr" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hr-management.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="super_hr">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Super HR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Super HR</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Rosi" required>
                        <div class="form-text">
                            Username login otomatis: <code>nama@warungsatelanud.id</code>, password default <code>password123</code>.<br>
                            Super HR otomatis mendapat akses semua cabang dan bisa mengelola HR biasa & Cabang,
                            tapi tetap tidak bisa mengakses Activity Log atau mengelola sesama Super HR/Owner.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection