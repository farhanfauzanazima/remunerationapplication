@extends('layouts.app')

@section('title', 'Statistik')
@section('page-title', 'Statistik Tren Gaji')

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>Periode</th><th>Total Gaji</th></tr></thead>
            <tbody>
                @forelse($trend as $t)
                <tr><td>{{ $t['period'] }}</td><td>Rp{{ number_format($t['total'], 0, ',', '.') }}</td></tr>
                @empty
                <tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection