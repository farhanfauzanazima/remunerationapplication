@extends('layouts.app')

@section('title', 'Detail Slip Gaji')
@section('page-title', 'Detail Slip Gaji')
@section('page-subtitle', $slip['employee']['name'] ?? '')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="160">Nama</td>
                            <td>: {{ $slip['employee']['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jabatan</td>
                            <td>: {{ $slip['employee']['position']['name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Cabang</td>
                            <td>: {{ $slip['employee']['branch']['name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td>: {{ $slip['payroll_period']['name'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($type === 'tetap')
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Hari Kerja / Masuk</td>
                            <td class="text-end">{{ $slip['hari_kerja'] }} / {{ $slip['masuk'] }}</td>
                        </tr>
                        <tr>
                            <td>Total Shift</td>
                            <td class="text-end">Rp{{ number_format($slip['total_shift'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Full</td>
                            <td class="text-end">Rp{{ number_format($slip['total_full'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Parsial</td>
                            <td class="text-end">Rp{{ number_format($slip['total_parsial'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gaji Pokok</strong></td>
                            <td class="text-end"><strong>Rp{{ number_format($slip['gaji_pokok'], 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Tunjangan Transport</td>
                            <td class="text-end">Rp{{ number_format($slip['tunjangan_transport'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Tunjangan Jabatan</td>
                            <td class="text-end">Rp{{ number_format($slip['tunjangan_jabatan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>BPJS</td>
                            <td class="text-end">Rp{{ number_format($slip['tunjangan_bpjs'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Tunjangan Masa Kerja</td>
                            <td class="text-end">Rp{{ number_format($slip['tunjangan_masa_kerja'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus Disiplin</td>
                            <td class="text-end">Rp{{ number_format($slip['bonus_disiplin'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus Omset</td>
                            <td class="text-end">Rp{{ number_format($slip['bonus_omset'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus Kinerja</td>
                            <td class="text-end">Rp{{ number_format($slip['bonus_kinerja'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jam Lembur / Total Lembur</td>
                            <td class="text-end">{{ $slip['jam_lembur'] }} jam /
                                Rp{{ number_format($slip['lembur'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Telat</td>
                            <td class="text-end">{{ $slip['telat'] }} hari</td>
                        </tr>
                        <tr>
                            <td>Tabungan Karyawan</td>
                            <td class="text-end">- Rp{{ number_format($slip['tabungan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Kasbon</td>
                            <td class="text-end">- Rp{{ number_format($slip['cashbond'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td>TAKE HOME PAY</td>
                            <td class="text-end">Rp{{ number_format($slip['thp'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td>TOTAL</td>
                            <td class="text-end">Rp{{ number_format($slip['total_gaji'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Full / Shift / Reguler</td>
                            <td class="text-end">{{ $slip['full'] }} / {{ $slip['shift'] }} / {{ $slip['reguler'] }}</td>
                        </tr>
                        <tr>
                            <td>Tunjangan</td>
                            <td class="text-end">Rp{{ number_format($slip['tunjangan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Full</td>
                            <td class="text-end">Rp{{ number_format($slip['total_full'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Shift</td>
                            <td class="text-end">Rp{{ number_format($slip['total_shift'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Reguler</td>
                            <td class="text-end">Rp{{ number_format($slip['total_reguler'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Transport</td>
                            <td class="text-end">Rp{{ number_format($slip['total_transport'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus</td>
                            <td class="text-end">Rp{{ number_format($slip['bonus'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td>TOTAL FEE</td>
                            <td class="text-end">Rp{{ number_format($slip['total_fee'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <a href="{{ route('salary-slips.edit', ['type' => $type, 'id' => $slip['id']]) }}"
                class="btn btn-outline-secondary">Edit</a>
            <a href="{{ route('salary-slips.preview-pdf', ['type' => $type, 'id' => $slip['id']]) }}" target="_blank"
                class="btn btn-outline-primary">
                <i class="bi bi-eye"></i> Preview PDF
            </a>
            <a href="{{ route('salary-slips.download-pdf', ['type' => $type, 'id' => $slip['id']]) }}"
                class="btn btn-outline-success">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <form action="{{ route('salary-slips.generate-link', ['type' => $type, 'id' => $slip['id']]) }}" method="POST"
                class="d-inline">
                @csrf
                <button class="btn btn-outline-info"><i class="bi bi-link-45deg"></i> Buat Link Publik</button>
            </form>
            <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-dark">Kembali</a>
        </div>
    </div>
@endsection
