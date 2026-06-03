@extends('layouts.app')

@section('title', 'Detail Slip Gaji')
@section('page-title', 'Detail Slip Gaji')
@section('page-subtitle', 'Informasi lengkap slip gaji karyawan')

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
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
            <h1>Slip Gaji #{{ str_pad($slip['id'], 6, '0', STR_PAD_LEFT) }}</h1>
            <p>{{ $slip['period']['period_name'] ?? '-' }} •
                {!! statusBadge($slip['status']) !!}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if ($slip['status'] === 'draft')
                <a href="{{ route('salary-slips.edit', $slip['id']) }}" class="btn btn-outline-secondary fw-600">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Info Karyawan --}}
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-person-fill"></i>
                        Data Karyawan
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div
                            style="width:52px;height:52px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:20px;color:#212529;">
                            {{ strtoupper(substr($slip['employee']['full_name'] ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-700 fs-14">
                                {{ $slip['employee']['full_name'] ?? '-' }}
                            </div>
                            <div class="fs-13 text-muted">
                                {{ $slip['employee']['employee_code'] ?? '-' }}
                            </div>
                        </div>
                    </div>

                    @php $entries = [['Email', $slip['employee']['email'] ?? '-'], ['Telepon', $slip['employee']['phone'] ?? '-'], ['Kategori', $slip['category']['category_name'] ?? '-'], ['Periode', $slip['period']['period_name'] ?? '-']]; @endphp

                    @foreach ($entries as [$label, $value])
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #F0F0F0;">
                            <span class="text-muted fs-13">{{ $label }}</span>
                            <span class="fw-600 fs-13">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kehadiran --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-calendar-check-fill"></i>
                        Kehadiran
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="fw-700" style="font-size:24px;color:var(--primary);">
                                {{ $slip['total_working_days'] }}
                            </div>
                            <div class="fs-13 text-muted">Hari Masuk</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-700" style="font-size:24px;color:#DC3545;">
                                {{ $slip['late_count'] }}
                            </div>
                            <div class="fs-13 text-muted">Terlambat</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-700" style="font-size:24px;color:#198754;">
                                {{ rupiah($slip['bonus']) != rupiah(0) ? '✓' : '-' }}
                            </div>
                            <div class="fs-13 text-muted">Bonus</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Gaji --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-cash-stack"></i>
                        Rincian Gaji
                    </div>
                </div>
                <div class="card-body">
                    <table class="w-100" style="font-size:15px;">
                        <tr>
                            <td class="py-3 text-muted">Gaji Pokok</td>
                            <td class="py-3 text-end fw-600">
                                {{ rupiah($slip['base_salary_amount']) }}
                            </td>
                        </tr>
                        <tr style="border-top:1px solid #F0F0F0;">
                            <td class="py-3 text-muted">Tunjangan</td>
                            <td class="py-3 text-end fw-600">
                                {{ rupiah($slip['allowance_amount']) }}
                            </td>
                        </tr>
                        @if (($slip['bonus'] ?? 0) > 0)
                            <tr style="border-top:1px solid #F0F0F0;">
                                <td class="py-3 text-muted">Bonus</td>
                                <td class="py-3 text-end fw-600 text-success">
                                    + {{ rupiah($slip['bonus']) }}
                                </td>
                            </tr>
                        @endif
                        <tr style="border-top:2px dashed #dee2e6;">
                            <td class="py-3 fw-700">Subtotal Pendapatan</td>
                            <td class="py-3 text-end fw-700">
                                {{ rupiah(($slip['base_salary_amount'] ?? 0) + ($slip['allowance_amount'] ?? 0) + ($slip['bonus'] ?? 0)) }}
                            </td>
                        </tr>
                        @if (($slip['late_penalty_amount'] ?? 0) > 0)
                            <tr style="border-top:1px solid #F0F0F0;">
                                <td class="py-3 text-muted">
                                    Potongan Terlambat
                                    ({{ $slip['late_count'] }}x ×
                                    {{ rupiah($slip['category']['late_penalty'] ?? 0) }})
                                </td>
                                <td class="py-3 text-end fw-600 text-danger">
                                    - {{ rupiah($slip['late_penalty_amount']) }}
                                </td>
                            </tr>
                        @endif
                        @if (($slip['additional_deduction'] ?? 0) > 0)
                            <tr style="border-top:1px solid #F0F0F0;">
                                <td class="py-3 text-muted">Potongan Tambahan</td>
                                <td class="py-3 text-end fw-600 text-danger">
                                    - {{ rupiah($slip['additional_deduction']) }}
                                </td>
                            </tr>
                        @endif
                        <tr style="border-top:3px solid var(--primary);background:#FFF3CD;">
                            <td class="py-4 fw-700" style="padding-left:12px;font-size:16px;">
                                TOTAL GAJI BERSIH
                            </td>
                            <td class="py-4 text-end fw-700 text-success" style="padding-right:12px;font-size:22px;">
                                {{ rupiah($slip['total_salary']) }}
                            </td>
                        </tr>
                    </table>

                    @if ($slip['notes'])
                        <div class="mt-3 p-3 rounded fs-13"
                            style="background:#FFF3CD;border-left:4px solid var(--primary);">
                            <strong>Catatan:</strong> {{ $slip['notes'] }}
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2 mt-4 flex-wrap">

                        {{-- Generate PDF --}}
                        <form action="{{ route('salary-slips.generate-pdf', $slip['id']) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary fw-600">
                                <i class="bi bi-gear me-2"></i>Generate PDF
                            </button>
                        </form>

                        {{-- Preview PDF --}}
                        <a href="{{ route('salary-slips.preview-pdf', $slip['id']) }}" target="_blank"
                            class="btn btn-outline-secondary fw-600">
                            <i class="bi bi-eye me-2"></i>Preview PDF
                        </a>

                        {{-- Download PDF --}}
                        <a href="{{ route('salary-slips.download-pdf', $slip['id']) }}" class="btn btn-danger fw-600">
                            <i class="bi bi-file-pdf me-2"></i>Download PDF
                        </a>

                        {{-- Kirim Email --}}
                        <a href="{{ route('emails.send', $slip['id']) }}" class="btn btn-success fw-600">
                            <i class="bi bi-envelope-fill me-2"></i>Kirim Email
                        </a>

                        {{-- Riwayat Email --}}
                        <a href="{{ route('emails.slip-history', $slip['id']) }}" class="btn btn-outline-secondary fw-600">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Email
                        </a>

                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
