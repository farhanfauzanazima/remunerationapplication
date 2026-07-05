@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            @include('employees._form', ['employee' => null])
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection