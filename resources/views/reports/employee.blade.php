@extends('layouts.app')

@section('title', 'Riwayat Gaji Karyawan')
@section('page-title', 'Riwayat Gaji Karyawan')

@section('content')
@if($report)
<h5>{{ $report['employee']['name'] }}</h5>
<table class="table table-bordered mt-3">
    <thead><tr><th>Periode</th><th>Total</th></tr></thead>
    <tbody>
        @foreach($report['slips'] as $slip)
        <tr>
            <td>{{ $slip['payroll_period']['name'] ?? '-' }}</td>
            <td>Rp{{ number_format($slip['total_gaji'] ?? $slip['total_fee'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="alert alert-warning">Data tidak ditemukan.</div>
@endif
@endsection