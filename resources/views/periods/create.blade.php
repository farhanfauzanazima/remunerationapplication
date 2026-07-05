@extends('layouts.app')

@section('title', 'Tambah Periode')
@section('page-title', 'Tambah Periode Penggajian')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('periods.store') }}" method="POST">
            @csrf
            @include('periods._form', ['period' => null])
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('periods.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection