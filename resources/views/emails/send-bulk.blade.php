@extends('layouts.app')

@section('title', 'Kirim Email Massal')
@section('page-title', 'Kirim Email Massal')
@section('page-subtitle', 'Kirim slip gaji ke semua karyawan sekaligus')

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
                <li class="breadcrumb-item active">Kirim Massal</li>
            </ol>
        </nav>
        <h1>Kirim Email Massal</h1>
        <p>Kirim slip gaji ke semua karyawan dalam satu periode</p>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-send-fill"></i>
                    Pilih Periode & Kirim
                </div>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
                @endif

                {{-- Form Pilih Periode --}}
                <form method="GET" action="{{ route('emails.send-bulk') }}" class="mb-4">
                    <label class="form-label fw-600">
                        Pilih Periode untuk Preview
                    </label>
                    <div class="d-flex gap-2">
                        <select name="period_id" class="form-select" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periods as $period)
                            <option value="{{ $period['id'] }}"
                                    {{ $selectedId == $period['id'] ? 'selected' : '' }}>
                                {{ $period['period_name'] }}
                                ({{ $period['status'] === 'open' ? 'Aktif' : 'Closed' }})
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-secondary fw-600">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                @if($selectedId)
                <form action="{{ route('emails.send-bulk.post') }}" method="POST" id="sendBulkForm">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $selectedId }}">

                    @if(count($slips) > 0)

                    <div class="alert-custom alert-info mb-3">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>
                            <strong>{{ count($slips) }} slip</strong> siap dikirim.
                            Semua memiliki status draft.
                        </span>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               id="selectAll"
                               onchange="toggleAll(this)">
                        <label class="form-check-label fw-600" for="selectAll">
                            Pilih Semua Karyawan
                        </label>
                    </div>

                    <div style="max-height:300px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;"
                         class="mb-4">
                        @foreach($slips as $slip)
                        <div class="form-check mb-2">
                            <input class="form-check-input slip-check"
                                   type="checkbox"
                                   name="slip_ids[]"
                                   value="{{ $slip['id'] }}"
                                   id="slip-{{ $slip['id'] }}"
                                   checked>
                            <label class="form-check-label" for="slip-{{ $slip['id'] }}">
                                <span class="fw-600">
                                    {{ $slip['employee']['full_name'] ?? '-' }}
                                </span>
                                <span class="fs-13 text-muted ms-1">
                                    — {{ rupiah($slip['total_salary']) }}
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit"
                            class="btn btn-success fw-600 w-100"
                            id="sendBtn"
                            onclick="return confirmSend()">
                        <i class="bi bi-send-fill me-2"></i>
                        Kirim Email ke <span id="selectedCount">{{ count($slips) }}</span>
                        Karyawan
                    </button>

                    @else
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">✅</div>
                        <h5>Semua Sudah Terkirim</h5>
                        <p>Tidak ada slip dengan status draft di periode ini.</p>
                    </div>
                    @endif
                </form>
                @endif

            </div>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Informasi Pengiriman Email
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 rounded mb-3"
                             style="background:#FFF3CD;border-left:4px solid var(--primary);">
                            <div class="fw-700 mb-2">
                                <i class="bi bi-envelope-fill me-2"></i>
                                Yang Dikirim ke Karyawan:
                            </div>
                            <ul style="margin:0;padding-left:20px;font-size:14px;">
                                <li>Email HTML berisi rincian gaji lengkap</li>
                                <li>File PDF slip gaji sebagai lampiran</li>
                                <li>Subjek: "Slip Gaji - [Nama] - [Periode]"</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded"
                             style="background:#D1E7DD;border-left:4px solid #198754;">
                            <div class="fw-700 mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Setelah Email Terkirim:
                            </div>
                            <ul style="margin:0;padding-left:20px;font-size:14px;">
                                <li>Status slip berubah dari Draft → Terkirim</li>
                                <li>Riwayat pengiriman tercatat otomatis</li>
                                <li>Dapat dikirim ulang jika diperlukan</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded"
                             style="background:#CFE2FF;border-left:4px solid #0D6EFD;">
                            <div class="fw-700 mb-2">
                                <i class="bi bi-gear-fill me-2"></i>
                                Driver Email Aktif:
                            </div>
                            <div class="fs-14">
                                @php
                                    $mailer = config('mail.default', env('MAIL_MAILER', 'smtp'));
                                @endphp
                                @if($mailer === 'resend')
                                <span class="badge-custom badge-sent">Resend API</span>
                                Cocok untuk pengiriman massal produksi
                                @else
                                <span class="badge-custom badge-open">SMTP Gmail</span>
                                Cocok untuk development & testing
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleAll(checkbox) {
    const checks = document.querySelectorAll('.slip-check');
    checks.forEach(c => c.checked = checkbox.checked);
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.slip-check:checked').length;
    const el = document.getElementById('selectedCount');
    if (el) el.textContent = checked;
}

function confirmSend() {
    const count = document.querySelectorAll('.slip-check:checked').length;
    if (count === 0) {
        alert('Pilih minimal satu karyawan untuk dikirim.');
        return false;
    }
    return confirm('Kirim slip gaji ke ' + count + ' karyawan sekarang?');
}

// Update count saat checkbox diubah
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.slip-check').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    // Set selectAll state
    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = true;
});
</script>
@endpush