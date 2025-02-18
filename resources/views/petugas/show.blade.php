@extends('partial.main')

@section('body')
<div class="container">
    <h1>Detail Petugas</h1>

    <div class="card">
        <img src="{{ asset('storage/' . $petugas->photo) }}" alt="Petugas Photo" class="card-img-top">
        <div class="card-body">
            <h5 class="card-title">{{ $petugas->name }}</h5>
            <p class="card-text">
                <strong>Jabatan:</strong> {{ $petugas->jabatan }}<br>
                <strong>Tempat Bertugas:</strong> {{ $petugas->tempat_bertugas }}<br>
                <strong>Email:</strong> {{ $petugas->email }}<br>
                <strong>Nomor HP:</strong> {{ $petugas->phone }}<br>
                <strong>Status:</strong> {{ $petugas->status }}<br>
                <strong>Pendidikan Terakhir:</strong> {{ $petugas->pendidikan_terakhir }}<br>
                <strong>Tahun Bergabung:</strong> {{ $petugas->tahun_bergabung }}<br>
            </p>
        </div>
    </div>
    <a href="{{ route('petugas.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
