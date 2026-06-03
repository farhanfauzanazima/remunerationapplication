@extends('layouts.app')

@section('title', 'Riwayat Email Slip')
@section('page-title', 'Riwayat Pengiriman Email')
@section('page-subtitle', 'Histori pengiriman email untuk slip ini')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('emails.index') }}">Distribusi Email</a>
                </li>
                <li class="breadcrumb-item active">Riwayat Slip</li>
            </ol>
        </nav>
        <h1>Riwayat Email: {{ $slip['employee']['full_name'] ?? '-' }}</h1>
        <p>{{ $slip['period']['period_name'] ?? '-' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('salary-slips.show', $slip['id']) }}"
           class="btn btn-outline-secondary fw-600">
            <i class="bi bi-file-earmark-text me-2"></i>Lihat Slip
        </a>
        <form action="{{ route('emails.resend', $slip['id']) }}"
              method="POST"
              onsubmit="return confirm('Kirim ulang email ke {{ $slip['employee']['email'] ?? '' }}?')">
            @csrf
            <button type="submit" class="btn btn-success fw-600">
                <i class="bi bi-arrow-repeat me-2"></i>Kirim Ulang
            </button>
        </form>
    </div>
</div>

{{-- Info Slip --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="fs-13 text-muted">Karyawan</div>
                <div class="fw-600">{{ $slip['employee']['full_name'] ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Email</div>
                <div class="fw-600">{{ $slip['employee']['email'] ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Total Gaji</div>
                <div class="fw-600 text-success">{{ rupiah($slip['total_salary']) }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-13 text-muted">Status Slip</div>
                {!! statusBadge($slip['status']) !!}
            </div>
        </div>
    </div>
</div>

{{-- Riwayat --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-clock-history"></i>
            Histori Pengiriman ({{ count($histories) }}x)
        </div>
    </div>
    <div class="card-body p-0">
        @if(count($histories) > 0)
        <div style="overflow-x:auto;">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Waktu Kirim</th>
                        <th>Email Tujuan</th>
                        <th>Status</th>
                        <th>Dikirim Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $h)
                    <tr>
                        <td>
                            {{ $h['sent_at']
                                ? \Carbon\Carbon::parse($h['sent_at'])->format('d M Y H:i')
                                : \Carbon\Carbon::parse($h['created_at'])->format('d M Y H:i') }}
                        </td>
                        <td class="fs-13">{{ $h['email_to'] }}</td>
                        <td>
                            @php $statusMap = [
                                'sent'    => '<span class="badge-custom badge-sent">Terkirim</span>',
                                'failed'  => '<span class="badge-custom badge-inactive">Gagal</span>',
                                'pending' => '<span class="badge-custom badge-draft">Pending</span>',
                            ]; @endphp
                            {!! $statusMap[$h['status']] ?? $h['status'] !!}
                        </td>
                        <td class="fs-13">{{ $h['sent_by'] ?? '-' }}</td>
                        <td class="fs-13 text-danger">
                            {{ $h['error'] ?? ($h['status'] === 'sent' ? '✓ Berhasil' : '-') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📧</div>
            <h5>Belum Pernah Dikirim</h5>
            <p>Slip ini belum pernah dikirim via email.</p>
        </div>
        @endif
    </div>
</div>

@endsection