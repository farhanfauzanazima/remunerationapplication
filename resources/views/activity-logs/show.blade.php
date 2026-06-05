@extends('layouts.app')

@section('title', 'Detail Log')
@section('page-title', 'Detail Activity Log')
@section('page-subtitle', 'Informasi lengkap aktivitas')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('activity-logs.index') }}">Activity Log</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
        <h1>Detail Log #{{ $log['id'] }}</h1>
        <p>{{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i:s') }}</p>
    </div>
    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary fw-600">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Informasi Aktivitas
                </div>
            </div>
            <div class="card-body">
                @php $rows = [
                    ['Pengguna',    $log['user']['name'] ?? '-'],
                    ['Role',        ucfirst($log['user']['role'] ?? '-')],
                    ['Aksi',        ucfirst(str_replace('_', ' ', $log['action']))],
                    ['Modul',       ucfirst($log['module'])],
                    ['Deskripsi',   $log['description']],
                    ['IP Address',  $log['ip_address'] ?? '-'],
                    ['Waktu',       \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i:s')],
                ]; @endphp

                @foreach($rows as [$label, $value])
                <div class="d-flex py-2" style="border-bottom:1px solid #F0F0F0;gap:16px;">
                    <div class="text-muted fs-13 fw-600" style="min-width:100px;">
                        {{ $label }}
                    </div>
                    <div class="fw-600 fs-13">{{ $value }}</div>
                </div>
                @endforeach

                @if($log['user_agent'] ?? false)
                <div class="d-flex py-2 gap-4">
                    <div class="text-muted fs-13 fw-600" style="min-width:100px;">
                        Browser
                    </div>
                    <div class="fs-13 text-muted" style="word-break:break-all;">
                        {{ Str::limit($log['user_agent'], 100) }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        {{-- Data Lama --}}
        @if($log['old_data'] ?? false)
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-clock-history"></i>
                    Data Sebelum Perubahan
                </div>
            </div>
            <div class="card-body">
                <pre style="background:#F8F9FA;padding:12px;border-radius:6px;font-size:12px;overflow:auto;max-height:200px;margin:0;">{{ json_encode($log['old_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        {{-- Data Baru --}}
        @if($log['new_data'] ?? false)
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-arrow-right-circle-fill"></i>
                    Data Setelah Perubahan
                </div>
            </div>
            <div class="card-body">
                <pre style="background:#F8F9FA;padding:12px;border-radius:6px;font-size:12px;overflow:auto;max-height:200px;margin:0;">{{ json_encode($log['new_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        @if(!($log['old_data'] ?? false) && !($log['new_data'] ?? false))
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-4">
                    <div class="empty-state-icon">📝</div>
                    <p>Tidak ada data perubahan untuk log ini.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection