@extends('partial.main')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4">Galeri Kegiatan</h1>

    @auth
    @if(in_array(auth()->user()->role, ['Admin', 'Kader'])) 
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('galeri.create') }}" class="btn btn-primary">Tambah Galeri</a>
        </div>
    @endif
    @endauth

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        @foreach($galeri as $item)
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="position-relative">
                    <img src="{{ asset('storage/' . $item->gambar) }}" class="card-img-top rounded-top" alt="{{ $item->judul }}" style="height: 200px; object-fit: cover;">
                    <span class="position-absolute top-0 start-0 bg-primary text-white px-3 py-1 small rounded-end">Kegiatan</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-truncate">{{ $item->judul }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($item->deskripsi, 100) }}</p>
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                    @auth
                    @if(auth()->user()->role === 'Admin') <!-- Hanya admin yang bisa mengedit dan menghapus -->
                    <a href="{{ route('informasi.galeri.edit', $item->id) }}" class="btn btn-info btn-sm">Edit</a>
                    <form action="{{ route('galeri.destroy', $item->id) }}" method="POST" class="mb-0">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                    @endif
                    @endauth
                    <a href="{{ route('galeri.show', $item->id) }}" class="btn btn-outline-primary btn-sm">Selengkapnya</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $galeri->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

    <style>
        /* Gaya untuk tampilan card */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* Style pada badge */
        .position-absolute {
            border-radius: 0 8px 8px 0;
        }

        /* Teks yang lebih ramah di layar kecil */
        .card-title {
            font-size: 1rem;
        }

        .card-text {
            font-size: 0.875rem;
        }
    </style>
