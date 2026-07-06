@extends('layouts.app')

@section('title', 'Rekap Gaji')
@section('page-title', 'Rekap Gaji per Periode')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="payroll_period_id" class="form-select" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p['id'] }}" {{ ($filters['payroll_period_id'] ?? '') == $p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b['id'] }}" {{ ($filters['branch_id'] ?? '') == $b['id'] ? 'selected' : '' }}>{{ $b['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Tampilkan</button></div>
        </form>
    </div>
</div>

@if($summary)
<div class="card mb-3">
    <div class="card-body">
        <h5>Grand Total: Rp{{ number_format($summary['grand_total'], 0, ',', '.') }}</h5>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>Cabang</th><th>Jumlah Slip Tetap</th><th>Total Tetap</th><th>Jumlah Slip Partime</th><th>Total Partime</th></tr></thead>
            <tbody>
                @forelse($summary['per_cabang'] as $branch => $data)
                <tr>
                    <td>{{ $branch }}</td>
                    <td>{{ $data['jumlah_tetap'] ?? 0 }}</td>
                    <td>Rp{{ number_format($data['total_tetap'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $data['jumlah_partime'] ?? 0 }}</td>
                    <td>Rp{{ number_format($data['total_partime'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection