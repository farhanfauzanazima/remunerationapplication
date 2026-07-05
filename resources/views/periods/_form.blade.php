<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Periode</label>
        <input type="text" name="name" class="form-control" placeholder="Contoh: Gaji Mei 2026"
            value="{{ old('name', $period['name'] ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Bulan</label>
        <select name="month" class="form-select" required>
            <option value="">-- Pilih Bulan --</option>
            @foreach($bulanIndo as $num => $label)
                <option value="{{ $num }}" {{ old('month', $period['month'] ?? '') == $num ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="year" class="form-control" placeholder="2026" min="2000" max="2100"
            value="{{ old('year', $period['year'] ?? date('Y')) }}" required>
    </div>

    <div class="col-12">
        <label class="form-label">Catatan <span class="text-muted">(opsional)</span></label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $period['notes'] ?? '') }}</textarea>
    </div>
</div>
<hr class="my-4">