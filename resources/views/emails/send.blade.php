@extends('layouts.app')

@section('title', 'Kirim Slip Gaji')
@section('page-title', 'Kirim Slip Gaji via Email')
@section('page-subtitle', 'Konfirmasi pengiriman slip gaji')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('salary-slips.index') }}">Slip Gaji</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('salary-slips.show', $slip['id']) }}">
                        Detail Slip
                    </a>
                </li>
                <li class="breadcrumb-item active">Kirim Email</li>
            </ol>
        </nav>
        <h1>Kirim Slip Gaji</h1>
        <p>Konfirmasi sebelum mengirim ke karyawan</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">

        {{-- Info Slip --}}
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    Detail Slip yang Akan Dikirim
                </div>
            </div>
            <div class="card-body">
                @php $rows = [
                    ['Karyawan',  $slip['employee']['full_name'] ?? '-'],
                    ['Email',     $slip['employee']['email'] ?? '-'],
                    ['Periode',   $slip['period']['period_name'] ?? '-'],
                    ['Kategori',  $slip['category']['category_name'] ?? '-'],
                    ['Total Gaji',rupiah($slip['total_salary'])],
                    ['Status',    ucfirst($slip['status'])],
                ]; @endphp

                @foreach($rows as [$label, $value])
                <div class="d-flex justify-content-between py-2"
                     style="border-bottom:1px solid #F0F0F0;">
                    <span class="text-muted fs-13">{{ $label }}</span>
                    <span class="fw-600 fs-13">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Info Email --}}
        <div class="alert-custom alert-info mb-4">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <div class="fw-600">Email akan dikirim ke:</div>
                <div class="fs-13 mt-1">
                    {{ $slip['employee']['email'] ?? '-' }}
                </div>
                <div class="fs-13 mt-1">
                    Slip gaji PDF akan dilampirkan secara otomatis.
                </div>
            </div>
        </div>

        @if($slip['status'] === 'sent')
        <div class="alert-custom alert-warning mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Slip ini sudah pernah dikirim sebelumnya.
            Mengirim ulang akan mengirimkan email baru.
        </div>
        @endif

        {{-- Form Konfirmasi --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('emails.send.post', $slip['id']) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success fw-600 flex-fill">
                            <i class="bi bi-send-fill me-2"></i>
                            Ya, Kirim Sekarang
                        </button>
                        <a href="{{ route('salary-slips.show', $slip['id']) }}"
                           class="btn btn-outline-secondary fw-600">
                            <i class="bi bi-x me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection