@extends('layouts.app')

@section('title', 'Edit Cabang')
@section('page-title', 'Edit Cabang')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('branches.update', $branch['id']) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Cabang</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $branch['name']) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $branch['address']) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch['phone']) }}">
            </div>
            <button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection