<!-- Data Orang Tua -->
<h2>Data Orang Tua</h2>
<div>
    <label>Nama Ibu</label>
    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $profile->nama_ibu ?? '') }}">
</div>
<div>
    <label>Usia Ibu</label>
    <input type="number" name="usia_ibu" value="{{ old('usia_ibu', $profile->usia_ibu ?? '') }}">
</div>
<div>
    <label>Pendidikan Ibu</label>
    <input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu', $profile->pendidikan_ibu ?? '') }}">
</div>
<div>
    <label>Pekerjaan Ibu</label>
    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $profile->pekerjaan_ibu ?? '') }}">
</div>
<div>
    <label>Nama Ayah</label>
    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $profile->nama_ayah ?? '') }}">
</div>
<div>
    <label>Pendidikan Ayah</label>
    <input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah', $profile->pendidikan_ayah ?? '') }}">
</div>
<div>
    <label>Pekerjaan Ayah</label>
    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $profile->pekerjaan_ayah ?? '') }}">
</div>

<!-- Data Anak -->
<h2>Data Anak</h2>
<div>
    <label>Nama Anak</label>
    <input type="text" name="nama_anak" value="{{ old('nama_anak', $profile->nama_anak ?? '') }}">
</div>
<div>
    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin">
        <option value="Laki-Laki" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
        <option value="Perempuan" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
    </select>
</div>
<div>
    <label>Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}">
</div>
<div>
    <label>Alamat</label>
    <input type="text" name="alamat" value="{{ old('alamat', $profile->alamat ?? '') }}">
</div>
<div>
    <label>Anak Ke-</label>
    <input type="number" name="anak_ke" value="{{ old('anak_ke', $profile->anak_ke ?? '') }}">
</div>
<div>
    <label>Status ASI</label>
    <select name="status_asi">
        <option value="Ekslusif" {{ old('status_asi', $profile->status_asi ?? '') == 'Ekslusif' ? 'selected' : '' }}>Ekslusif</option>
        <option value="Tidak Ekslusif" {{ old('status_asi', $profile->status_asi ?? '') == 'Tidak Ekslusif' ? 'selected' : '' }}>Tidak Ekslusif</option>
    </select>
</div>
<div>
    <label>Status Imunisasi</label>
    <select name="status_imunisasi">
        <option value="Lengkap" {{ old('status_imunisasi', $profile->status_imunisasi ?? '') == 'Lengkap' ? 'selected' : '' }}>Lengkap</option>
        <option value="Tidak Lengkap" {{ old('status_imunisasi', $profile->status_imunisasi ?? '') == 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
    </select>
</div>
<div>
    <label>Berat Badan Lahir (kg)</label>
    <input type="number" name="bb_lahir" step="0.01" value="{{ old('bb_lahir', $profile->bb_lahir ?? '') }}">
</div>
<div>
    <label>Tinggi Badan Lahir (cm)</label>
    <input type="number" name="tb_lahir" step="0.1" value="{{ old('tb_lahir', $profile->tb_lahir ?? '') }}">
</div>

<!-- Data Sanitasi -->
<h2>Data Sanitasi</h2>
<div>
    <label>Kepemilikan Jamban</label>
    <select name="kepemilikan_jamban">
        <option value="Ada" {{ old('kepemilikan_jamban', $profile->kepemilikan_jamban ?? '') == 'Ada' ? 'selected' : '' }}>Ada</option>
        <option value="Tidak Ada" {{ old('kepemilikan_jamban', $profile->kepemilikan_jamban ?? '') == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
    </select>
</div>
<div>
    <label>Luas Rumah</label>
    <input type="text" name="luas_rumah" value="{{ old('luas_rumah', $profile->luas_rumah ?? '') }}">
</div>
<div>
    <label>Lantai Rumah</label>
    <input type="text" name="lantai_rumah" value="{{ old('lantai_rumah', $profile->lantai_rumah ?? '') }}">
</div>
<div>
    <label>Jumlah Penghuni</label>
    <input type="number" name="jml_penghuni" value="{{ old('jml_penghuni', $profile->jml_penghuni ?? '') }}">
</div>
<div>
    <label>Alat Masak</label>
    <input type="text" name="alat_masak" value="{{ old('alat_masak', $profile->alat_masak ?? '') }}">
</div>
<div>
    <label>Sumber Air</label>
    <input type="text" name="sumber_air" value="{{ old('sumber_air', $profile->sumber_air ?? '') }}">
</div>
<div>
    <label>Riwayat Kesehatan</label>
    <input type="text" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan', $profile->riwayat_kesehatan ?? '') }}">
</div>
