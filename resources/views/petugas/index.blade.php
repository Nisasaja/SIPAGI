@extends('partial.main')

@section('body')
<div class="container my-5">
    <h1 class="my-4 text-center text-primary fw-bold">Daftar Petugas Kesehatan</h1>
    <a href="{{ route('petugas.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-person-plus-fill"></i> Tambah Petugas
    </a>

    <div class="row">
        @foreach ($petugas as $petugasItem)
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card shadow-sm border-light rounded-4">
                <div class="d-flex justify-content-center pt-3">
                    <div class="card-img-container">
                        <img src="{{ asset('storage/' . $petugasItem->photo) }}" 
                            alt="Petugas Photo" class="card-img-top">
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center fw-bold text-dark">{{ $petugasItem->name }}</h5>

                    <div class="info-container">
                        <p><strong class="text-dark">Tanggal Lahir:</strong> <span class="text-secondary">{{ $petugasItem->birthdate }}</span></p>
                        <p><strong class="text-dark">Alamat:</strong> <span class="text-secondary">{{ $petugasItem->address }}</span></p>
                        <p><strong class="text-dark">Jenis Kelamin:</strong> <span class="text-secondary">{{ $petugasItem->jenis_kelamin }}</span></p>
                        <p>
                            <strong class="text-dark">Status:</strong> 
                            <span class="badge {{ $petugasItem->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $petugasItem->status }}
                            </span>
                        </p>
                        <p><strong class="text-dark">Jabatan:</strong> <span class="text-secondary">{{ $petugasItem->jabatan }}</span></p>
                        <p><strong class="text-dark">Tempat Bertugas:</strong> <span class="text-secondary">{{ $petugasItem->tempat_bertugas }}</span></p>
                        <p><strong class="text-dark">Pendidikan Terakhir:</strong> <span class="text-secondary">{{ $petugasItem->pendidikan_terakhir }}</span></p>
                        <p><strong class="text-dark">Tahun Bergabung:</strong> <span class="text-secondary">{{ $petugasItem->tahun_bergabung }}</span></p>
                        <p>
                            <strong class="text-dark">BPJS:</strong> 
                            <span class="badge {{ $petugasItem->bpjs == 'Tidak Ada' ? 'bg-secondary' : 'bg-primary' }}">
                                {{ $petugasItem->bpjs }}
                            </span>
                        </p>
                        @if($petugasItem->bpjs != 'Tidak Ada')
                        <p><strong class="text-dark">Nomor BPJS:</strong> <span class="text-secondary">{{ $petugasItem->nomor_bpjs }}</span></p>
                        @endif
                        <p><strong class="text-dark">NIK:</strong> <span class="text-secondary">{{ $petugasItem->nik }}</span></p>
                        <p><strong class="text-dark">Email:</strong> <span class="text-secondary">{{ $petugasItem->email }}</span></p>
                        <p><strong class="text-dark">Nomor HP:</strong> <span class="text-secondary">{{ $petugasItem->phone }}</span></p>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('petugas.edit', $petugasItem->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </a>
                        <form action="{{ route('petugas.destroy', $petugasItem->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash-fill"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

    <style>
        /* Kontainer untuk pas foto */
        .card-img-container {
            width: 120px; /* Ukuran pas foto standar */
            height: 160px; /* Menyesuaikan rasio 3:4 */
            border: 3px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Pastikan pas foto memiliki ukuran yang sesuai */
        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Styling untuk kartu */
        .card {
            transition: transform 0.2s ease-in-out;
            padding: 15px;
            background: #ffffff;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        }

        .info-container p {
            margin: 3px 0;
            font-size: 0.9rem;
        }

        /* Warna teks utama & isi lebih redup */
        .text-dark {
            font-weight: 600;
            color: #333; /* Hitam pekat untuk judul */
        }

        .text-secondary {
            color: #6c757d; /* Abu-abu untuk informasi */
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background: #007bff;
            color: #fff;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: #fff;
        }
    </style>
