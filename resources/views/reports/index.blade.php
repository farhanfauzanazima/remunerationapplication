@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<div class="row g-3">
    <div class="col-md-6">
        <a href="{{ route('reports.salary-summary') }}" class="text-decoration-none">
            <div class="card h-100"><div class="card-body">
                <i class="bi bi-cash-stack fs-2 text-primary"></i>
                <h5 class="mt-2">Rekap Gaji per Periode</h5>
                <p class="text-muted mb-0">Total gaji tetap & partime per cabang dalam satu periode.</p>
            </div></div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('reports.statistics') }}" class="text-decoration-none">
            <div class="card h-100"><div class="card-body">
                <i class="bi bi-bar-chart-fill fs-2 text-success"></i>
                <h5 class="mt-2">Statistik Tren Gaji</h5>
                <p class="text-muted mb-0">Tren total pengeluaran gaji 6 periode terakhir.</p>
            </div></div>
        </a>
    </div>
</div>
@endsection