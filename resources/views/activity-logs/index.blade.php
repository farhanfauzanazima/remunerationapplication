@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Catatan aktivitas pengguna sistem')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Activity Log</li>
            </ol>
        </nav>
        <h1>Activity Log</h1>
        <p>Catatan seluruh aktivitas pengguna di sistem</p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('activity-logs.index') }}"
              class="row g-2 align-items-end">

            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Modul</label>
                <select name="module" class="form-select">
                    <option value="">Semua Modul</option>
                    @foreach(['auth','salary','employee','period','email','report','category'] as $mod)
                    <option value="{{ $mod }}"
                            {{ request('module') === $mod ? 'selected' : '' }}>
                        {{ ucfirst($mod) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua Aksi</option>
                    @foreach(['login','logout','create','update','delete','change_password'] as $act)
                    <option value="{{ $act }}"
                            {{ request('action') === $act ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $act)) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Dari Tanggal</label>
                <input type="date"
                       name="date_from"
                       class="form-control"
                       value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Sampai Tanggal</label>
                <input type="date"
                       name="date_to"
                       class="form-control"
                       value="{{ request('date_to') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Per Halaman</label>
                <select name="per_page" class="form-select">
                    @foreach([10, 20, 50] as $n)
                    <option value="{{ $n }}"
                            {{ request('per_page', 20) == $n ? 'selected' : '' }}>
                        {{ $n }} data
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-600 flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('activity-logs.index') }}"
                       class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($error)
<div class="alert-custom alert-error mb-4">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $error }}
</div>
@endif

{{-- Tabel Log --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($logs) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Pengguna</th>
                        <th width="10%">Aksi</th>
                        <th width="10%">Modul</th>
                        <th>Deskripsi</th>
                        <th width="12%">IP Address</th>
                        <th width="15%">Waktu</th>
                        <th width="5%" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $i => $log)
                    @php
                        $actionColors = [
                            'login'           => 'badge-open',
                            'logout'          => 'badge-closed',
                            'create'          => 'badge-sent',
                            'update'          => 'badge-draft',
                            'delete'          => 'badge-inactive',
                            'change_password' => 'badge-owner',
                        ];
                        $actionColor = $actionColors[$log['action']] ?? 'badge-draft';
                    @endphp
                    <tr>
                        <td class="text-center text-muted">
                            {{ ($pagination['current_page'] - 1) * ($pagination['per_page'] ?? 20) + $i + 1 }}
                        </td>
                        <td>
                            <div class="fw-700 fs-13">
                                {{ $log['user']['name'] ?? '-' }}
                            </div>
                            <div class="fs-13">
                                <span class="badge-custom badge-{{ $log['user']['role'] ?? 'admin' }} "
                                      style="font-size:10px;padding:2px 6px;">
                                    {{ ucfirst($log['user']['role'] ?? '-') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-custom {{ $actionColor }}"
                                  style="font-size:11px;">
                                {{ ucfirst(str_replace('_', ' ', $log['action'])) }}
                            </span>
                        </td>
                        <td class="fs-13">{{ ucfirst($log['module']) }}</td>
                        <td class="fs-13">{{ Str::limit($log['description'], 80) }}</td>
                        <td class="fs-13 text-muted font-monospace">
                            {{ $log['ip_address'] ?? '-' }}
                        </td>
                        <td class="fs-13">
                            {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i:s') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('activity-logs.show', $log['id']) }}"
                               class="btn-action view"
                               title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(!empty($pagination) && ($pagination['last_page'] ?? 1) > 1)
        <div class="d-flex justify-content-between align-items-center p-3"
             style="border-top:1px solid var(--border);">
            <div class="fs-13 text-muted">
                Menampilkan {{ count($logs) }} dari {{ $pagination['total'] ?? 0 }} log
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if(($pagination['current_page'] ?? 1) > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Sebelumnya
                </a>
                @endif

                <span class="fs-13 fw-600 px-2">
                    Halaman {{ $pagination['current_page'] ?? 1 }}
                    dari {{ $pagination['last_page'] ?? 1 }}
                </span>

                @if(($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1))
                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    Berikutnya <i class="bi bi-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @endif

        @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h5>Belum Ada Activity Log</h5>
            <p>Log aktivitas akan muncul setelah pengguna melakukan aksi di sistem.</p>
        </div>
        @endif
    </div>
</div>

@endsection