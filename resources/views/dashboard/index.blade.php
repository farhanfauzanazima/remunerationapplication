@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan penggajian ' . (session('user.role') === 'owner' ? 'seluruh cabang' : 'cabang yang Anda kelola'))

@section('content')

{{-- Periode Terakhir — global, tidak per cabang, sesuai permintaan tidak ada perubahan --}}
<div class="card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted small">Periode Terakhir</div>
            <div class="fs-5 fw-bold">{{ $stats['periode_terakhir']['name'] ?? '-' }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('salary-slips.bulk-create') }}" class="btn btn-primary"><i class="bi bi-collection-fill"></i> Input Massal Slip Gaji</a>
            <a href="{{ route('distribution.index') }}" class="btn btn-outline-success"><i class="bi bi-send-fill"></i> Distribusi Gaji</a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-bar-chart-fill"></i> Lihat Laporan</a>
        </div>
    </div>
</div>

{{-- Statistik per Cabang --}}
@forelse($stats['branches'] ?? [] as $branch)
<div class="mb-4">
    <h6 class="text-muted mb-2"><i class="bi bi-shop"></i> {{ $branch['branch_name'] }}</h6>
    <div class="row g-3">
        <div class="col-md-4 d-flex">
            <div class="card h-100 w-100"><div class="card-body">
                <div class="text-muted small">Karyawan Aktif</div>
                <div class="fs-3 fw-bold">{{ $branch['karyawan_aktif'] }}</div>
                <div class="text-muted small">{{ $branch['karyawan_tetap'] }} Tetap · {{ $branch['karyawan_partime'] }} Partime</div>
            </div></div>
        </div>
        <div class="col-md-4 d-flex">
            <div class="card h-100 w-100"><div class="card-body">
                <div class="text-muted small">Total Gaji Periode Terakhir</div>
                <div class="fs-5 fw-bold">Rp{{ number_format($branch['total_gaji_periode_terakhir'], 0, ',', '.') }}</div>
                <div class="text-muted small">{{ $branch['total_slip_periode_terakhir'] }} slip diproses</div>
            </div></div>
        </div>
        <div class="col-md-4 d-flex">
            <div class="card h-100 w-100"><div class="card-body">
                <div class="text-muted small">Distribusi</div>
                <div class="fs-6 mt-2">
                    <span class="badge bg-success">{{ $branch['distribusi']['terkirim'] }} Terkirim</span>
                    <span class="badge bg-danger">{{ $branch['distribusi']['gagal'] }} Gagal</span>
                    <span class="badge bg-secondary">{{ $branch['distribusi']['pending'] }} Pending</span>
                </div>
            </div></div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">Belum ada cabang yang bisa ditampilkan.</div>
@endforelse

{{-- Grafik Tren — HANYA muncul jika backend mengirim key "trend" (khusus Owner) --}}
@if(isset($stats['trend']))
<div class="row g-3 mt-1">
    <div class="col-md-6 d-flex">
        <div class="card h-100 w-100"><div class="card-body">
            <h6 class="mb-3">Tren Gaji Seluruh Cabang (6 Periode Terakhir)</h6>
            <canvas id="chartOverall" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-md-6 d-flex">
        <div class="card h-100 w-100"><div class="card-body">
            <h6 class="mb-3">Tren Gaji per Cabang</h6>
            <canvas id="chartPerBranch" height="220"></canvas>
        </div></div>
    </div>
</div>
@endif

@endsection

@if(isset($stats['trend']))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const TREND = @json($stats['trend']);

const overallLabels = TREND.overall.map(t => t.period);
const overallData = TREND.overall.map(t => t.total);

new Chart(document.getElementById('chartOverall'), {
    type: 'line',
    data: {
        labels: overallLabels,
        datasets: [{
            label: 'Total Gaji',
            data: overallData,
            borderColor: '#f7c948',
            backgroundColor: 'rgba(247,201,72,0.2)',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp' + Number(v).toLocaleString('id-ID') } } }
    }
});

const branchColors = ['#f7c948', '#4e73df', '#1cc88a', '#e74a3b', '#36b9cc', '#858796'];
const branchNames = Object.keys(TREND.per_branch);
const branchLabels = branchNames.length ? TREND.per_branch[branchNames[0]].map(t => t.period) : [];

const datasets = branchNames.map((name, i) => ({
    label: name,
    data: TREND.per_branch[name].map(t => t.total),
    borderColor: branchColors[i % branchColors.length],
    backgroundColor: 'transparent',
    tension: 0.3,
}));

new Chart(document.getElementById('chartPerBranch'), {
    type: 'line',
    data: { labels: branchLabels, datasets },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { ticks: { callback: v => 'Rp' + Number(v).toLocaleString('id-ID') } } }
    }
});
</script>
@endpush
@endif