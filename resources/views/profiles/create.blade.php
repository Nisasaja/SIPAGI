@extends('partial.main')

@section('body')
<link rel="stylesheet" href="{{ asset('asset/css/create.css') }}">
<div class="container">
    <h1>{{ isset($profile) ? 'Edit Profil' : 'Tambah Data Profil Balita' }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
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
        <div class="form-container" style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Data Orang Tua Card -->
            <div class="card form-card">
                <div class="card-header bg-primary text-white">
                    <i class="fa-solid fa-user pe-2"></i>
                    <h5 class="mb-0" >Data Orang Tua</h5>
                </div>
                <div class="card-body" padding:"15px" "grid-template-columns: 1fr 1fr">
                    <div class="form-group">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" id="nama_ibu" value="{{ isset($profile) ? $profile->nama_ibu : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="usia_ibu">Usia Ibu</label>
                        <input type="number" name="usia_ibu" class="form-control" id="usia_ibu" value="{{ isset($profile) ? $profile->usia_ibu : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="pendidikan_ibu">Pendidikan Ibu</label>
                        <input type="text" name="pendidikan_ibu" class="form-control" id="pendidikan_ibu" value="{{ isset($profile) ? $profile->pendidikan_ibu : '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control" id="pekerjaan_ibu" value="{{ isset($profile) ? $profile->pekerjaan_ibu : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_ayah">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control" id="nama_ayah" value="{{ isset($profile) ? $profile->nama_ayah : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="pendidikan_ayah">Pendidikan Ayah</label>
                        <input type="text" name="pendidikan_ayah" class="form-control" id="pendidikan_ayah" value="{{ isset($profile) ? $profile->pendidikan_ayah : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control" id="pekerjaan_ayah" value="{{ isset($profile) ? $profile->pekerjaan_ayah : '' }}" required>
                    </div>
                </div>
            </div>

            <!-- Data Balita Card -->
            <div class="card form-card">
                <div class="card-header bg-success text-white">
                    <i class="fa-solid fa-child pe-2"></i>
                    <h5 class="mb-0">Data Balita</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_anak">Nama Anak</label>
                        <input type="text" name="nama_anak" class="form-control" id="nama_anak" value="{{ isset($profile) ? $profile->nama_anak : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" id="jenis_kelamin" required>
                            <option value="Laki-Laki" {{ isset($profile) && $profile->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ isset($profile) && $profile->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="{{ isset($profile) ? $profile->tanggal_lahir : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Asal Desa</label>
                        <select name="alamat" class="form-control" id="alamat" required>
                            <option value="Sekatak" {{ isset($profile) && $profile->alamat == 'Sekatak' ? 'selected' : '' }}>Sekatak</option>
                            <option value="Salimbatu" {{ isset($profile) && $profile->alamat == 'Salimbatu' ? 'selected' : '' }}>Salimbatu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="anak_ke">Anak Ke</label>
                        <input type="number" name="anak_ke" class="form-control" id="anak_ke" value="{{ isset($profile) ? $profile->anak_ke : '' }}" required>
                    </div>
               
                    <div class="form-group">
                        <label for="status_asi">Status ASI</label>
                        <select name="status_asi" class="form-control" id="status_asi" required>
                            <option value="Ekslusif" {{ isset($profile) && $profile->status_asi == 'Ekslusif' ? 'selected' : '' }}>Eksklusif</option>
                            <option value="Tidak Ekslusif" {{ isset($profile) && $profile->status_asi == 'Tidak Ekslusif' ? 'selected' : '' }}>Tidak Eksklusif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status_imunisasi">Status Imunisasi</label>
                        <select name="status_imunisasi" class="form-control" id="status_imunisasi" required>
                            <option value="Lengkap" {{ isset($profile) && $profile->status_imunisasi == 'Lengkap' ? 'selected' : '' }}>Lengkap</option>
                            <option value="Tidak Lengkap" {{ isset($profile) && $profile->status_imunisasi == 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bb_lahir">BB Lahir (kg)</label>
                        <input type="number" name="bb_lahir" class="form-control" id="bb_lahir" value="{{ isset($profile) ? $profile->bb_lahir : '' }}" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tb_lahir">TB Lahir (cm)</label>
                        <input type="number" name="tb_lahir" class="form-control" id="tb_lahir" value="{{ isset($profile) ? $profile->tb_lahir : '' }}" step="0.01" required>
                    </div>        
                </div>
            </div>

            <!-- Data Sanitasi dan Riwayat Kesehatan Card -->
            <div class="card form-card">
                <div class="card-header bg-info text-white">
                    <i class="fa-solid fa-hand-holding-droplet pe-2"></i>
                    <h5 class="mb-0">Data Sanitasi dan Riwayat Kesehatan</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="kepemilikan_jamban">Kepemilikan Jamban</label>
                        <select name="kepemilikan_jamban" class="form-control" id="kepemilikan_jamban" required>
                            <option value="Ada" {{ isset($profile) && $profile->kepemilikan_jamban == 'Ada' ? 'selected' : '' }}>Ada</option>
                            <option value="Tidak Ada" {{ isset($profile) && $profile->kepemilikan_jamban == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="luas_rumah">Luas Rumah (m²)</label>
                        <input type="text" name="luas_rumah" class="form-control" id="luas_rumah" value="{{ isset($profile) ? $profile->luas_rumah : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="lantai_rumah">Lantai Rumah</label>
                        <input type="text" name="lantai_rumah" class="form-control" id="lantai_rumah" value="{{ isset($profile) ? $profile->lantai_rumah : '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="jml_penghuni">Jumlah Penghuni</label>
                        <input type="number" name="jml_penghuni" class="form-control" id="jml_penghuni" value="{{ isset($profile) ? $profile->jml_penghuni : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="alat_masak">Alat Masak</label>
                        <input type="text" name="alat_masak" class="form-control" id="alat_masak" value="{{ isset($profile) ? $profile->alat_masak : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="sumber_air">Sumber Air</label>
                        <input type="text" name="sumber_air" class="form-control" id="sumber_air" value="{{ isset($profile) ? $profile->sumber_air : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="riwayat_kesehatan">Riwayat Kesehatan</label>
                        <input type="text" name="riwayat_kesehatan" class="form-control" id="riwayat_kesehatan" value="{{ isset($profile) ? $profile->riwayat_kesehatan : '' }}" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">{{ isset($profile) ? 'Update' : 'Simpan' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
