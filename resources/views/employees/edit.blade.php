@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('employees.update', $employee['id']) }}" method="POST">
            @csrf @method('PUT')
            @include('employees._form', ['employee' => $employee])
            <button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection