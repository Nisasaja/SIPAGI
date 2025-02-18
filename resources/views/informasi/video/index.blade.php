@extends('partial.main')

@section('body')
<div class="container">
    <h1 class="text-center my-4">Daftar Video Edukasi</h1>

    <!-- Tampilkan tombol Tambah Video hanya untuk Admin -->
    @if (auth()->user()->role === 'Admin')
        <a href="{{ route('informasi.video.create') }}" class="btn btn-primary mb-3">Tambah Video</a>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @foreach($videos as $video)
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <!-- Menggunakan embed untuk memutar video -->
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="{{ $video->url }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $video->title }}</h5>
                        <p class="card-text text-muted">{{ $video->description }}</p>
                        
                        <!-- Tampilkan tombol Edit dan Hapus hanya untuk Admin -->
                        @if (auth()->user()->role === 'Admin')
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('informasi.video.edit', $video->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('informasi.video.destroy', $video->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .ratio {
            background: #000;
        }

        /* CSS untuk mengatur panjang teks deskripsi */
        .card-body .card-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; /* Jangan biarkan teks melompat ke baris berikutnya */
            height: 60px; /* Tentukan tinggi yang sesuai dengan jumlah baris yang diinginkan */
        }
    </style>

