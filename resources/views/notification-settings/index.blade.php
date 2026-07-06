@extends('layouts.app')

@section('title', 'Template Pesan WhatsApp')
@section('page-title', 'Template Pesan WhatsApp')
@section('page-subtitle', 'Atur isi pesan yang dikirim saat distribusi gaji via WhatsApp')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('notification-settings.update') }}" method="POST">
            @csrf @method('PUT')

            <label class="form-label">Isi Pesan</label>
            <textarea name="whatsapp_template" class="form-control" rows="5" required>{{ old('whatsapp_template', $setting['whatsapp_template'] ?? '') }}</textarea>

            <div class="form-text mt-2">
                Placeholder yang bisa dipakai:
                @foreach($placeholders as $p)
                    <code class="me-1">{{ $p }}</code>
                @endforeach
                <br>
                <code>{nama}</code> = nama karyawan, <code>{bulan}</code> = nama bulan, <code>{tahun}</code> = tahun periode, <code>{link}</code> = link download PDF (otomatis).
            </div>

            <button class="btn btn-primary mt-3">
                <i class="bi bi-check-lg"></i> Simpan Template
            </button>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header fw-semibold">Contoh Pratinjau</div>
    <div class="card-body">
        <p class="mb-0" id="previewText" style="white-space: pre-line;"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.querySelector('textarea[name="whatsapp_template"]');
    const preview = document.getElementById('previewText');

    function updatePreview() {
        let text = textarea.value;
        text = text.replace('{nama}', 'Rahmat Maulidin')
                    .replace('{bulan}', 'Mei')
                    .replace('{tahun}', '2026')
                    .replace('{link}', 'https://domainanda.com/public/slip/abcxyz123');
        preview.textContent = text;
    }

    textarea.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush