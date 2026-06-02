@extends('layouts.app')

@section('title', 'Periode Penggajian')
@section('page-title', 'Periode Penggajian')
@section('page-subtitle', 'Kelola periode penggajian karyawan')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Periode Penggajian</li>
            </ol>
        </nav>
        <h1>Periode Penggajian</h1>
        <p>Total {{ count($periods) }} periode terdaftar</p>
    </div>
    <div>
        <a href="{{ route('periods.create') }}" class="btn btn-primary fw-600">
            <i class="bi bi-plus-circle me-2"></i>Tambah Periode
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('periods.index') }}"
              class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fs-13 fw-600">Cari Periode</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Nama periode..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="open"
                            {{ request('status') === 'open' ? 'selected' : '' }}>
                        Open
                    </option>
                    <option value="closed"
                            {{ request('status') === 'closed' ? 'selected' : '' }}>
                        Closed
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-600 flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('periods.index') }}"
                       class="btn btn-outline-secondary fw-600">
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

{{-- Tabel Periode --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($periods) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Periode</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Akhir</th>
                        <th>Durasi</th>
                        <th>Catatan</th>
                        <th width="10%">Status</th>
                        <th width="18%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periods as $i => $period)
                    @php
                        $start    = \Carbon\Carbon::parse($period['start_date']);
                        $end      = \Carbon\Carbon::parse($period['end_date']);
                        $duration = $start->diffInDays($end) + 1;
                        $isOpen   = $period['status'] === 'open';
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-700">{{ $period['period_name'] }}</div>
                            <div class="fs-13 text-muted">
                                ID: #{{ $period['id'] }}
                            </div>
                        </td>
                        <td>{{ $start->format('d M Y') }}</td>
                        <td>{{ $end->format('d M Y') }}</td>
                        <td>
                            <span class="badge-custom badge-open">
                                {{ $duration }} hari
                            </span>
                        </td>
                        <td class="fs-13 text-muted">
                            {{ $period['notes'] ?? '-' }}
                        </td>
                        <td>{!! statusBadge($period['status']) !!}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center flex-wrap">

                                {{-- Edit (hanya jika open) --}}
                                @if($isOpen)
                                <a href="{{ route('periods.edit', $period['id']) }}"
                                   class="btn-action edit"
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @endif

                                {{-- Close / Reopen --}}
                                @if($isOpen)
                                <form action="{{ route('periods.close', $period['id']) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Tutup periode {{ $period['period_name'] }}?\nPeriode yang ditutup tidak dapat diedit.')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="btn-action"
                                            style="background:#FFF3CD;color:#856404;"
                                            title="Tutup Periode">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('periods.reopen', $period['id']) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Buka kembali periode {{ $period['period_name'] }}?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="btn-action"
                                            style="background:#D1E7DD;color:#198754;"
                                            title="Buka Kembali">
                                        <i class="bi bi-unlock-fill"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Delete (hanya jika open) --}}
                                @if($isOpen)
                                <button type="button"
                                        class="btn-action delete"
                                        title="Hapus"
                                        onclick="confirmDelete({{ $period['id'] }}, '{{ $period['period_name'] }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                <form id="deleteForm-{{ $period['id'] }}"
                                      action="{{ route('periods.destroy', $period['id']) }}"
                                      method="POST"
                                      style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📅</div>
            <h5>Belum Ada Periode Penggajian</h5>
            <p>Buat periode penggajian untuk mulai mengelola slip gaji karyawan.</p>
            <a href="{{ route('periods.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Buat Periode Pertama
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Info --}}
<div class="card mt-4">
    <div class="card-body">
        <div class="row g-3 fs-14">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge-custom badge-open">Open</span>
                    <span>Periode aktif, slip gaji masih bisa dibuat dan diedit</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-custom badge-closed">Closed</span>
                    <span>Periode ditutup, tidak bisa membuat slip gaji baru</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="btn-action" style="background:#FFF3CD;color:#856404;width:24px;height:24px;">
                        <i class="bi bi-lock-fill" style="font-size:12px;"></i>
                    </span>
                    <span>Tutup periode (tidak dapat diedit setelah ditutup)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="btn-action" style="background:#D1E7DD;color:#198754;width:24px;height:24px;">
                        <i class="bi bi-unlock-fill" style="font-size:12px;"></i>
                    </span>
                    <span>Buka kembali periode yang sudah ditutup</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm('Hapus periode "' + name + '"?\n\nPeriode yang memiliki slip gaji tidak dapat dihapus.')) {
        document.getElementById('deleteForm-' + id).submit();
    }
}
</script>
@endpush