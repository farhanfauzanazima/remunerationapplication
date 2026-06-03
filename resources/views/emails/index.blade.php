@extends('layouts.app')

@section('title', 'Distribusi Email')
@section('page-title', 'Distribusi Email')
@section('page-subtitle', 'Kelola pengiriman slip gaji via email')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Distribusi Email</li>
            </ol>
        </nav>
        <h1>Distribusi Email</h1>
        <p>Riwayat pengiriman slip gaji via email</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('emails.send-bulk') }}" class="btn btn-primary fw-600">
            <i class="bi bi-send-fill me-2"></i>Kirim Email Massal
        </a>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    @php
        $totalSent    = collect($histories)->where('status', 'sent')->count();
        $totalFailed  = collect($histories)->where('status', 'failed')->count();
        $totalPending = collect($histories)->where('status', 'pending')->count();
    @endphp
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalSent }}</div>
                <div class="stat-label">Email Terkirim</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-envelope-x-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalFailed }}</div>
                <div class="stat-label">Email Gagal</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalPending }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('emails.index') }}"
              class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Periode</label>
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $period)
                    <option value="{{ $period['id'] }}"
                            {{ request('period_id') == $period['id'] ? 'selected' : '' }}>
                        {{ $period['period_name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-13 fw-600">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp['id'] }}"
                            {{ request('employee_id') == $emp['id'] ? 'selected' : '' }}>
                        {{ $emp['full_name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-13 fw-600">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>
                        Terkirim
                    </option>
                    <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>
                        Gagal
                    </option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-600 flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('emails.index') }}"
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

{{-- Tabel Riwayat --}}
<div class="card">
    <div class="card-body p-0">
        @if(count($histories) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Email Tujuan</th>
                        <th>Status</th>
                        <th>Dikirim Oleh</th>
                        <th>Waktu Kirim</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $i => $history)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-700">
                                {{ $history['employee']['full_name'] ?? '-' }}
                            </div>
                            <div class="fs-13 text-muted">
                                {{ $history['employee']['employee_code'] ?? '-' }}
                            </div>
                        </td>
                        <td class="fs-14">{{ $history['period'] ?? '-' }}</td>
                        <td class="fs-13">{{ $history['email_to'] }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'sent'    => '<span class="badge-custom badge-sent">Terkirim</span>',
                                    'failed'  => '<span class="badge-custom badge-inactive">Gagal</span>',
                                    'pending' => '<span class="badge-custom badge-draft">Pending</span>',
                                ];
                            @endphp
                            {!! $statusMap[$history['status']] ?? $history['status'] !!}
                        </td>
                        <td class="fs-13">{{ $history['sent_by'] ?? '-' }}</td>
                        <td class="fs-13">
                            {{ $history['sent_at']
                                ? \Carbon\Carbon::parse($history['sent_at'])->format('d M Y H:i')
                                : '-' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                {{-- Lihat detail slip --}}
                                <a href="{{ route('salary-slips.show', $history['slip_id']) }}"
                                   class="btn-action view"
                                   title="Lihat Slip">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                {{-- Kirim ulang jika gagal --}}
                                @if($history['status'] === 'failed')
                                <form action="{{ route('emails.resend', $history['slip_id']) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Kirim ulang email ke {{ $history['email_to'] }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="btn-action email"
                                            title="Kirim Ulang">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if($history['error'] ?? false)
                    <tr style="background:#FFF5F5;">
                        <td colspan="8" class="fs-13 text-danger py-1 ps-4">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Error: {{ $history['error'] }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(!empty($pagination) && ($pagination['last_page'] ?? 1) > 1)
        <div class="d-flex justify-content-between align-items-center p-3"
             style="border-top:1px solid var(--border);">
            <div class="fs-13 text-muted">
                Menampilkan {{ count($histories) }} dari
                {{ $pagination['total'] ?? 0 }} data
            </div>
            <div class="d-flex gap-2">
                @if(($pagination['current_page'] ?? 1) > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i>
                </a>
                @endif
                <span class="btn btn-sm btn-primary disabled">
                    {{ $pagination['current_page'] ?? 1 }}
                    / {{ $pagination['last_page'] ?? 1 }}
                </span>
                @if(($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1))
                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @endif

        @else
        <div class="empty-state">
            <div class="empty-state-icon">📧</div>
            <h5>Belum Ada Riwayat Pengiriman</h5>
            <p>Kirim slip gaji ke karyawan untuk melihat riwayat di sini.</p>
            <a href="{{ route('emails.send-bulk') }}" class="btn btn-primary mt-3">
                <i class="bi bi-send-fill me-2"></i>Kirim Email Massal
            </a>
        </div>
        @endif
    </div>
</div>

@endsection