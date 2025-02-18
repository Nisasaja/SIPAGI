@extends('partial.main')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Buku Resep Makanan Lokal</h1>
    <p class="text-center">Berikut adalah buku resep makanan lokal balita dan ibu hamil yang dirilis oleh Kementerian Kesehatan tahun 2023.</p>

    @auth
    <div class="text-center mb-4">
        <iframe src="{{ $pdfPath }}" width="100%" height="600px" style="border: none;"></iframe>
    </div>
    <div class="text-center">
        <a href="{{ $pdfPath }}" class="btn btn-primary" target="_blank">Unduh PDF</a>
    </div>
    @else
    <div class="alert alert-warning text-center">
        Anda harus <a href="{{ route('login') }}">login</a> untuk mengakses buku resep ini.
    </div>
    @endauth
</div>
@endsection
