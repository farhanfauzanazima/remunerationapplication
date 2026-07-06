@extends('layouts.app')

@section('title', 'Distribusi Gaji')
@section('page-title', 'Distribusi Gaji')
@section('page-subtitle', 'Kirim slip gaji ke karyawan via Email atau WhatsApp')

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Periode</label>
                    <select name="payroll_period_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periods as $p)
                            <option value="{{ $p['id'] }}" {{ $selectedPeriod == $p['id'] ? 'selected' : '' }}>
                                {{ $p['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cabang</label>
                    <select name="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b['id'] }}" {{ $selectedBranch == $b['id'] ? 'selected' : '' }}>
                                {{ $b['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if ($slipData)
        <form action="{{ route('distribution.send-bulk') }}" method="POST" id="distributionForm">
            @csrf
            <input type="hidden" name="channel" id="channelInput">

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Pilih Karyawan</span>
                    <div>
                        <button type="button" id="btnPilihSemua" class="btn btn-sm btn-outline-secondary">Pilih
                            Semua</button>
                        <button type="button" id="btnBatalSemua" class="btn btn-sm btn-outline-secondary">Batal
                            Semua</button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="40"></th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Email</th>
                                <th>No. HP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slipData['tetap'] ?? [] as $slip)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input chk-item" name="tetap_ids[]"
                                            value="{{ $slip['id'] }}"></td>
                                    <td>{{ $slip['employee']['name'] }}</td>
                                    <td><span class="badge bg-primary">Tetap</span></td>
                                    <td>{{ $slip['employee']['email'] ?? '-' }}</td>
                                    <td>{{ $slip['employee']['phone'] ?? '-' }}</td>
                                </tr>
                            @empty
                            @endforelse
                            @forelse($slipData['partime'] ?? [] as $slip)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input chk-item" name="partime_ids[]"
                                            value="{{ $slip['id'] }}"></td>
                                    <td>{{ $slip['employee']['name'] }}</td>
                                    <td><span class="badge bg-info">Tim Partime</span></td>
                                    <td>{{ $slip['employee']['email'] ?? '-' }}</td>
                                    <td>{{ $slip['employee']['phone'] ?? '-' }}</td>
                                </tr>
                            @empty
                            @endforelse

                            @if (empty($slipData['tetap']) && empty($slipData['partime']))
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada slip gaji untuk periode &
                                        cabang ini</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-distribusi" data-channel="email">
                        <i class="bi bi-envelope-fill"></i> Kirim via Email
                    </button>
                    <button type="button" class="btn btn-success btn-distribusi" data-channel="whatsapp">
                        <i class="bi bi-whatsapp"></i> Kirim via WhatsApp
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-info">Pilih Periode dan Cabang untuk menampilkan daftar karyawan.</div>
    @endif

    <div class="card mt-4">
        <div class="card-header fw-semibold">Riwayat Distribusi Terakhir</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Waktu</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                        <tr>
                            <td>{{ $h['employee_name'] ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $h['channel'] == 'email' ? 'bg-primary' : 'bg-success' }}">
                                    {{ ucfirst($h['channel']) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $h['status'] == 'sent' ? 'bg-success' : ($h['status'] == 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ ucfirst($h['status']) }}
                                </span>
                            </td>
                            <td>{{ $h['note'] ?? '-' }}</td>
                            <td>{{ $h['sent_at'] ? \Carbon\Carbon::parse($h['sent_at'])->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-end">
                                @if ($h['status'] !== 'sent')
                                    <form action="{{ route('distribution.resend', $h['id']) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Kirim ulang distribusi ini?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-repeat"></i>
                                            Kirim Ulang</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada riwayat distribusi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnPilihSemua = document.getElementById('btnPilihSemua');
            const btnBatalSemua = document.getElementById('btnBatalSemua');
            const form = document.getElementById('distributionForm');

            if (btnPilihSemua) {
                btnPilihSemua.addEventListener('click', function() {
                    document.querySelectorAll('.chk-item').forEach(el => el.checked = true);
                });
            }

            if (btnBatalSemua) {
                btnBatalSemua.addEventListener('click', function() {
                    document.querySelectorAll('.chk-item').forEach(el => el.checked = false);
                });
            }

            document.querySelectorAll('.btn-distribusi').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const channel = btn.dataset.channel;
                    const checked = document.querySelectorAll('.chk-item:checked');

                    if (checked.length === 0) {
                        alert('Pilih minimal satu karyawan terlebih dahulu.');
                        return;
                    }

                    const label = channel === 'email' ? 'Email' : 'WhatsApp';
                    if (!confirm(`Kirim slip gaji ke ${checked.length} karyawan via ${label}?`)) {
                        return;
                    }

                    document.getElementById('channelInput').value = channel;

                    document.querySelectorAll('.btn-distribusi').forEach(b => b.disabled = true);
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Mengirim, mohon tunggu...';

                    form.submit();
                });
            });
        });
    </script>
@endpush
