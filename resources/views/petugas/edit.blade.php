@extends('partial.main')

@section('body')
<div class="container">
    <h1>Edit Petugas</h1>

    <form action="{{ route('petugas.update', $petugas->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $petugas->name }}" required>
        </div>
        <div class="mb-3">
            <label for="birthdate" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $petugas->birthdate }}" required>
        </div>
        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label> 
            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Perempuan" {{ $petugas->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                <option value="Laki-laki" {{ $petugas->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <textarea class="form-control" id="address" name="address" required>{{ $petugas->address }}</textarea>
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Aktif" {{ $petugas->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ $petugas->status == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Pensiun" {{ $petugas->status == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                <option value="Tidak Aktif" {{ $petugas->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ $petugas->jabatan }}" required>
        </div>
        <div class="mb-3">
            <label for="tempat_bertugas" class="form-label">Tempat Bertugas</label>
            <input type="text" class="form-control" id="tempat_bertugas" name="tempat_bertugas" value="{{ $petugas->tempat_bertugas }}" required>
        </div>
        <div class="mb-3">
            <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
            <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" value="{{ $petugas->pendidikan_terakhir }}" required>
        </div>
        <div class="mb-3">
            <label for="tahun_bergabung" class="form-label">Tahun Bergabung</label>
            <input type="text" class="form-control" id="tahun_bergabung" name="tahun_bergabung" value="{{ $petugas->tahun_bergabung }}" required>
        </div>
        <div class="mb-3">
            <label for="bpjs">BPJS</label>
            <select name="bpjs" id="bpjs" class="form-control" required>
                <option value="Tidak Ada" {{ old('bpjs', $petugas->bpjs ?? '') == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada BPJS</option>
                <option value="BPJS Kesehatan" {{ old('bpjs', $petugas->bpjs ?? '') == 'BPJS Kesehatan' ? 'selected' : '' }}>BPJS Kesehatan/Pemerintah</option>
                <option value="BPJS Ketenagakerjaan" {{ old('bpjs', $petugas->bpjs ?? '') == 'BPJS Ketenagakerjaan' ? 'selected' : '' }}>BPJS Ketenagakerjaan</option>
                <option value="BPJS Kesehatan dan Ketenagakerjaan" {{ old('bpjs', $petugas->bpjs ?? '') == 'BPJS Kesehatan dan Ketenagakerjaan' ? 'selected' : '' }}>BPJS Kesehatan dan Ketenagakerjaan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nomor_bpjs" class="form-label">Nomor BPJS</label>
            <input type="text" class="form-control" id="nomor_bpjs" name="nomor_bpjs" value="{{ $petugas->nomor_bpjs }}" required>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" value="{{ $petugas->nik }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $petugas->email }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $petugas->phone }}" required>
        </div>
        <!-- Similar input fields for other attributes -->
        <div class="mb-3">
            <label for="photo" class="form-label">Foto</label>
            <input type="file" class="form-control" id="photo" name="photo">
            <img src="{{ asset('storage/' . $petugas->photo) }}" alt="Petugas Photo" class="mt-2" width="100">
        </div>
        <button type="submit" class="btn btn-warning">Update</button>
    </form>
</div>
@section('styles')
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        img {
            display: block;
            margin-top: 10px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .form-control {
                padding: 8px;
            }

            .btn-warning {
                padding: 8px 16px;
            }
        }
    </style>
@endsection
@endsection
