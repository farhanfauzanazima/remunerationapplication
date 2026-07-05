@extends('layouts.app')

@section('title', 'Edit Periode')
@section('page-title', 'Edit Periode Penggajian')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('periods.update', $period['id']) }}" method="POST">
            @csrf @method('PUT')
            @include('periods._form', ['period' => $period])
            <button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('periods.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection