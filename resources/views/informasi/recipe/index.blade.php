@extends('partial.main')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-3">Buku Resep Makanan Lokal</h1>
    <p class="text-center mb-4">Berikut adalah buku resep makanan lokal balita dan ibu hamil yang dirilis oleh Kementerian Kesehatan.</p>

    @auth
    <!-- Pilihan Buku dalam Navbar -->
    <div class="d-flex justify-content-center mb-4">
        <nav class="nav nav-pills">
            @foreach ($pdfFiles as $index => $file)
            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" href="#" onclick="showPdf('{{ $file['path'] }}', this)">{{ $file['title'] }}</a>
            @endforeach
        </nav>
    </div>

    <!-- Area untuk menampilkan PDF -->
    <div class="text-center">
        <iframe id="pdfViewer" src="{{ $pdfFiles[0]['path'] ?? '' }}" width="100%" height="600px" class="rounded shadow"></iframe>
    </div>

    @else
    <div class="alert alert-warning text-center">
        Anda harus <a href="{{ route('login') }}">login</a> untuk mengakses buku resep ini.
    </div>
    @endauth
</div>

    <script>
        function showPdf(pdfUrl, element) {
            document.getElementById('pdfViewer').src = pdfUrl;

            // Hapus kelas 'active' dari semua link
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

            // Tambahkan kelas 'active' ke link yang diklik
            element.classList.add('active');
        }
    </script>

    <style>
        .nav-pills .nav-link {
            font-weight: bold;
            color: #007bff;
            margin: 0 5px;
            border-radius: 5px;
        }
        .nav-pills .nav-link:hover, .nav-pills .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        iframe {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
