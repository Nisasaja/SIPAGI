@extends('partial.main')

@section('body')
<div class="container">
    <h1>Tambah Petugas</h1>

    <form action="{{ route('petugas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="birthdate" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
        </div>
        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Perempuan">Perempuan</option>
                <option value="Laki-laki">Laki-laki</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <textarea class="form-control" id="address" name="address" required></textarea>
        </div>
        <div class="mb-3">
        <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Aktif" {{ old('status', $petugas->status ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ old('status', $petugas->status ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Pensiun" {{ old('status', $petugas->status ?? '') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                <option value="Tidak Aktif" {{ old('status', $petugas->status ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" required>
        </div>
        <div class="mb-3">
            <label for="tempat_bertugas" class="form-label">Tempat Bertugas</label>
            <input type="text" class="form-control" id="tempat_bertugas" name="tempat_bertugas" required>
        </div>
        <div class="mb-3">
            <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
            <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
        </div>
        <div class="mb-3">
            <label for="tahun_bergabung" class="form-label">Tahun Bergabung</label>
            <input type="text" class="form-control" id="tahun_bergabung" name="tahun_bergabung" required>
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
            <input type="text" class="form-control" id="nomor_bpjs" name="nomor_bpjs">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" required>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Foto</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            <a href="{{ route('petugas.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </form>
</div>
@endsection
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        .form-label {
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-secondary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .text-danger {
            font-size: 14px;
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">