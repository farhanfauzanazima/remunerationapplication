@extends('layouts.app')

@section('title', 'Kategori Gaji')
@section('page-title', 'Kategori Gaji')
@section('page-subtitle', 'Kelola kategori dan komponen gaji karyawan')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Kategori Gaji</li>
            </ol>
        </nav>
        <h1>Kategori Gaji</h1>
        <p>Total {{ count($categories) }} kategori terdaftar</p>
    </div>
    <div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary fw-600">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
        </a>
    </div>
</div>

{{-- Error API --}}
@if($error)
<div class="alert-custom alert-error mb-4">
    <i class="bi bi-exclamation-circle-fill"></i>
    {{ $error }}
</div>
@endif

{{-- Tabel Kategori --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($categories) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan Terlambat</th>
                        <th>Rate Lembur</th>
                        <th width="10%">Status</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $i => $cat)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-700">{{ $cat['category_name'] }}</div>
                            @if($cat['description'])
                            <div class="fs-13 text-muted mt-1">{{ $cat['description'] }}</div>
                            @endif
                        </td>
                        <td class="fw-600 text-success">
                            {{ rupiah($cat['base_salary']) }}
                        </td>
                        <td>{{ rupiah($cat['allowance']) }}</td>
                        <td class="text-danger">
                            {{ rupiah($cat['late_penalty']) }}/x
                        </td>
                        <td class="text-muted">
                            {{ rupiah($cat['overtime_rate']) }}/jam
                        </td>
                        <td>
                            {!! statusBadge($cat['is_active'] ? 'active' : 'inactive') !!}
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('categories.edit', $cat['id']) }}"
                                   class="btn-action edit"
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button"
                                        class="btn-action delete"
                                        title="Hapus"
                                        onclick="confirmDelete({{ $cat['id'] }}, '{{ $cat['category_name'] }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>

                            {{-- Hidden form untuk delete --}}
                            <form id="deleteForm-{{ $cat['id'] }}"
                                  action="{{ route('categories.destroy', $cat['id']) }}"
                                  method="POST"
                                  style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🏷️</div>
            <h5>Belum Ada Kategori Gaji</h5>
            <p>Tambahkan kategori gaji untuk mulai mengelola penggajian karyawan.</p>
            <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Pertama
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Info Formula --}}
<div class="card mt-4">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-calculator-fill"></i>
            Formula Perhitungan Gaji
        </div>
    </div>
    <div class="card-body">
        <div style="background:#F8F9FA;border-radius:8px;padding:16px;font-family:'Courier New',monospace;font-size:14px;color:#212529;border-left:4px solid var(--primary);">
            Total Gaji = Gaji Pokok + Tunjangan + Bonus<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            - (Jumlah Terlambat × Potongan per Keterlambatan)<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            - Potongan Tambahan
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm('Hapus kategori "' + name + '"?\n\nKategori yang sedang digunakan karyawan tidak dapat dihapus.')) {
        document.getElementById('deleteForm-' + id).submit();
    }
}
</script>
@endpush