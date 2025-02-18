@extends('partial.main')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">{{ $galeri->judul }}</h1>
    <div class="text-center mb-4">
        <img src="{{ asset('storage/' . $galeri->gambar) }}" alt="{{ $galeri->judul }}" class="img-fluid" style="max-height: 400px; object-fit: cover;">
    </div>
    <div class="mb-4">
        <h5>Deskripsi</h5>
        <p>{{ $galeri->deskripsi }}</p>
    </div>
    <div class="d-flex justify-content-end">
        <a href="{{ route('galeri.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
