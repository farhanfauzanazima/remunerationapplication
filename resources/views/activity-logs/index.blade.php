@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Riwayat aktivitas pengguna di sistem')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="module" class="form-control" placeholder="Filter modul (contoh: employee)"
                    value="{{ $filters['module'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="action" class="form-control" placeholder="Filter aksi (contoh: create)"
                    value="{{ $filters['action'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Modul</th>
                    <th>Aksi</th>
                    <th class="text-end">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log['created_at'])->format('d/m/Y H:i') }}</td>
                    <td>{{ $log['user']['name'] ?? 'Sistem' }}</td>
                    <td><span class="badge bg-secondary">{{ $log['module'] }}</span></td>
                    <td>{{ $log['action'] }}</td>
                    <td class="text-end">
                        <a href="{{ route('activity-logs.show', $log['id']) }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada aktivitas tercatat</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($pagination)
        <div class="text-muted small mt-2">
            Halaman {{ $pagination['current_page'] ?? 1 }} dari {{ $pagination['last_page'] ?? 1 }}
            ({{ $pagination['total'] ?? count($logs) }} data)
        </div>
        @endif
    </div>
</div>
@endsection