@extends('partial.main')

@section('body')
    <div class="container mt-5">
        <h1 class="mb-4">Grafik KMS Balita</h1>

        <!-- Form Pencarian -->
        <form action="{{ route('grafik.index') }}" method="GET" class="mb-4">
            <div class="input-group" style="max-width: 400px; margin: auto;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama anak..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <!-- Daftar Profil -->
        <div class="row">
            @forelse($profiles as $profile)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $profile->nama_anak }}</h5>
                            <p class="card-text">Tanggal Lahir: {{ \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d M Y') }}</p>
                            <a href="{{ route('pengukuran.kms', ['id' => $profile->id]) }}" class="btn-custom">Lihat Grafik</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">Data tidak ditemukan.</p>
            @endforelse
        </div>
    </div>
@endsection

    <style>
        .btn-custom {
        background-color: #f56666; /* Warna dasar */
        color: #fff; /* Warna teks */
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #f78989; /* Warna saat hover */
        color: #fff;
    }

    </style>
