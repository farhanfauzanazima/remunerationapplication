@extends('layouts.app')

@section('title', 'Detail Activity Log')
@section('page-title', 'Detail Activity Log')

@section('content')
@if($log)
<div class="card">
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tr><td class="text-muted" width="160">Waktu</td><td>: {{ \Carbon\Carbon::parse($log['created_at'])->format('d/m/Y H:i:s') }}</td></tr>
            <tr><td class="text-muted">User</td><td>: {{ $log['user']['name'] ?? 'Sistem' }}</td></tr>
            <tr><td class="text-muted">Modul</td><td>: {{ $log['module'] }}</td></tr>
            <tr><td class="text-muted">Aksi</td><td>: {{ $log['action'] }}</td></tr>
        </table>

        <hr>
        <h6>Data Sebelum</h6>
        <pre class="bg-light p-3 rounded">{{ json_encode($log['old_data'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

        <h6>Data Sesudah</h6>
        <pre class="bg-light p-3 rounded">{{ json_encode($log['new_data'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
    <div class="card-footer">
        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-dark">Kembali</a>
    </div>
</div>
@else
<div class="alert alert-warning">Data tidak ditemukan.</div>
@endif
@endsection