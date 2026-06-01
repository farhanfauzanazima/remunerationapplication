@extends('layouts.app')

@section('title', 'Manajemen Karyawan')
@section('page-title', 'Karyawan')
@section('page-subtitle', 'Kelola data karyawan restoran')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Karyawan</li>
            </ol>
        </nav>
        <h1>Manajemen Karyawan</h1>
        <p>Total {{ count($employees) }} karyawan ditemukan</p>
    </div>
    <div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary fw-600">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan
        </a>
    </div>
</div>

{{-- Filter & Search --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fs-13 fw-600">Cari Karyawan</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Nama, email, atau kode..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}"
                            {{ request('category_id') == $cat['id'] ? 'selected' : '' }}>
                        {{ $cat['category_name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-600 flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Error --}}
@if($error)
<div class="alert-custom alert-error mb-4">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $error }}
</div>
@endif

{{-- Tabel Karyawan --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($employees) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Karyawan</th>
                        <th>Kategori Gaji</th>
                        <th>Kontak</th>
                        <th>Bergabung</th>
                        <th width="10%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $i => $emp)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#212529;flex-shrink:0;">
                                    {{ strtoupper(substr($emp['full_name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-700">{{ $emp['full_name'] }}</div>
                                    <div class="fs-13 text-muted">
                                        {{ $emp['employee_code'] ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-custom badge-open">
                                {{ $emp['category']['category_name'] ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="fs-14">{{ $emp['email'] }}</div>
                            <div class="fs-13 text-muted">{{ $emp['phone'] }}</div>
                        </td>
                        <td class="fs-14">
                            {{ $emp['join_date']
                                ? \Carbon\Carbon::parse($emp['join_date'])->format('d M Y')
                                : '-' }}
                        </td>
                        <td>
                            {!! statusBadge($emp['status']) !!}
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('employees.salary-history', $emp['id']) }}"
                                   class="btn-action view"
                                   title="Riwayat Gaji">
                                    <i class="bi bi-clock-history"></i>
                                </a>
                                <a href="{{ route('employees.edit', $emp['id']) }}"
                                   class="btn-action edit"
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button"
                                        class="btn-action delete"
                                        title="Hapus"
                                        onclick="confirmDelete({{ $emp['id'] }}, '{{ $emp['full_name'] }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>

                            <form id="deleteForm-{{ $emp['id'] }}"
                                  action="{{ route('employees.destroy', $emp['id']) }}"
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
            <div class="empty-state-icon">👥</div>
            <h5>Belum Ada Data Karyawan</h5>
            <p>Tambahkan data karyawan untuk mulai membuat slip gaji.</p>
            <a href="{{ route('employees.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan
            </a>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm('Hapus karyawan "' + name + '"?\n\nKaryawan yang memiliki riwayat slip gaji tidak dapat dihapus.')) {
        document.getElementById('deleteForm-' + id).submit();
    }
}
</script>
@endpush