@extends('partial.main')

@section('body')
<link rel="stylesheet" href="{{ asset('asset/css/edit.css') }}"
<div class="container my-5">
    <h1 class="mb-4">{{ isset($profile) ? 'Edit Profil' : 'Tambah Profil' }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($profile) ? route('profiles.update', $profile->id) : route('profiles.store') }}" method="POST">
        @csrf
        @if(isset($profile))
            @method('PUT')
        @endif

        <!-- Data Ibu Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Orang Tua</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" id="nama_ibu" value="{{ isset($profile) ? $profile->nama_ibu : '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="usia_ibu" class="form-label">Usia Ibu</label>
                        <input type="number" name="usia_ibu" class="form-control" id="usia_ibu" value="{{ isset($profile) ? $profile->usia_ibu : '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pendidikan_ibu" class="form-label">Pendidikan Ibu</label>
                        <input type="text" name="pendidikan_ibu" class="form-control" id="pendidikan_ibu" value="{{ isset($profile) ? $profile->pendidikan_ibu : '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control" id="pekerjaan_ibu" value="{{ isset($profile) ? $profile->pekerjaan_ibu : '' }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control" id="nama_ayah" value="{{ isset($profile) ? $profile->nama_ayah : '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pendidikan_ayah" class="form-label">Pendidikan Ayah</label>
                        <input type="text" name="pendidikan_ayah" class="form-control" id="pendidikan_ayah" value="{{ isset($profile) ? $profile->pendidikan_ayah : '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control" id="pekerjaan_ayah" value="{{ isset($profile) ? $profile->pekerjaan_ayah : '' }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{--  Data Anak  --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Data Anak </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Nama Anak -->
                    <div class="col-md-6 mb-3">
                        <label for="nama_anak" class="form-label">Nama Anak</label>
                        <input type="text" name="nama_anak" class="form-control" id="nama_anak" value="{{ isset($profile) ? $profile->nama_anak : '' }}" required>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-md-6 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" id="jenis_kelamin" required>
                            <option value="Laki-Laki" {{ isset($profile) && $profile->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ isset($profile) && $profile->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="{{ isset($profile) ? $profile->tanggal_lahir : '' }}" required>
                    </div>

                    <!-- Asal Desa -->
                    <div class="col-md-6 mb-3">
                        <label for="alamat" class="form-label">Asal Desa</label>
                        <select name="alamat" class="form-control" id="alamat" required>
                            <option value="Sekatak" {{ isset($profile) && $profile->alamat == 'Sekatak' ? 'selected' : '' }}>Sekatak</option>
                            <option value="Salimbatu" {{ isset($profile) && $profile->alamat == 'Salimbatu' ? 'selected' : '' }}>Salimbatu</option>
                        </select>
                    </div>

                    <!-- Anak Ke -->
                    <div class="col-md-6 mb-3">
                        <label for="anak_ke" class="form-label">Anak Ke</label>
                        <input type="number" name="anak_ke" class="form-control" id="anak_ke" value="{{ isset($profile) ? $profile->anak_ke : '' }}" required>
                    </div>

                    <!-- Status ASI -->
                    <div class="col-md-6 mb-3">
                        <label for="status_asi" class="form-label">Status ASI</label>
                        <input type="text" name="status_asi" class="form-control" id="status_asi" value="{{ isset($profile) ? $profile->status_asi : '' }}" required>
                    </div>

                    <!-- Status Imunisasi -->
                    <div class="col-md-6 mb-3">
                        <label for="status_imunisasi" class="form-label">Status Imunisasi</label>
                        <input type="text" name="status_imunisasi" class="form-control" id="status_imunisasi" value="{{ isset($profile) ? $profile->status_imunisasi : '' }}" required>
                    </div>

                    <!-- BB Lahir -->
                    <div class="col-md-6 mb-3">
                        <label for="bb_lahir" class="form-label">BB Lahir (kg)</label>
                        <input type="number" step="0.01" name="bb_lahir" class="form-control" id="bb_lahir" value="{{ isset($profile) ? $profile->bb_lahir : '' }}" required>
                    </div>

                    <!-- TB Lahir -->
                    <div class="col-md-6 mb-3">
                        <label for="tb_lahir" class="form-label">TB Lahir (cm)</label>
                        <input type="number" step="0.01" name="tb_lahir" class="form-control" id="tb_lahir" value="{{ isset($profile) ? $profile->tb_lahir : '' }}" required>
                    </div>
                </div>
            </div>
        </div>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Data Sanitasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kepemilikan_jamban" class="form-label">Kepemilikan Jamban</label>
                        <select name="kepemilikan_jamban" class="form-control" id="kepemilikan_jamban" required>
                            <option value="Ada" {{ isset($profile) && $profile->kepemilikan_jamban == 'Ada' ? 'selected' : '' }}>Ada</option>
                            <option value="Tidak Ada" {{ isset($profile) && $profile->kepemilikan_jamban == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                        </select>
                    </div>

                    <!-- Luas Rumah -->
                    <div class="col-md-6 mb-3">
                        <label for="luas_rumah" class="form-label">Luas Rumah (m²)</label>
                        <input type="text" name="luas_rumah" class="form-control" id="luas_rumah" value="{{ isset($profile) ? $profile->luas_rumah : '' }}" required>
                    </div>

                    <!-- Lantai Rumah -->
                    <div class="col-md-6 mb-3">
                        <label for="lantai_rumah" class="form-label">Lantai Rumah</label>
                        <input type="text" name="lantai_rumah" class="form-control" id="lantai_rumah" value="{{ isset($profile) ? $profile->lantai_rumah : '' }}" required>
                    </div>

                    <!-- Jumlah Penghuni -->
                    <div class="col-md-6 mb-3">
                        <label for="jml_penghuni" class="form-label">Jumlah Penghuni</label>
                        <input type="number" name="jml_penghuni" class="form-control" id="jml_penghuni" value="{{ isset($profile) ? $profile->jml_penghuni : '' }}" required>
                    </div>

                    <!-- Alat Masak -->
                    <div class="col-md-6 mb-3">
                        <label for="alat_masak" class="form-label">Alat Masak</label>
                        <input type="text" name="alat_masak" class="form-control" id="alat_masak" value="{{ isset($profile) ? $profile->alat_masak : '' }}" required>
                    </div>

                    <!-- Sumber Air -->
                    <div class="col-md-6 mb-3">
                        <label for="sumber_air" class="form-label">Sumber Air</label>
                        <input type="text" name="sumber_air" class="form-control" id="sumber_air" value="{{ isset($profile) ? $profile->sumber_air : '' }}" required>
                    </div>

                    <!-- Riwayat Kesehatan -->
                    <div class="col-md-6 mb-3">
                        <label for="riwayat_kesehatan" class="form-label">Riwayat Kesehatan</label>
                        <input type="text" name="riwayat_kesehatan" class="form-control" id="riwayat_kesehatan" value="{{ isset($profile) ? $profile->riwayat_kesehatan : '' }}" required>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">{{ isset($profile) ? 'Update' : 'Simpan' }}</button>
        </div>
    </form>
</div>
@endsection
