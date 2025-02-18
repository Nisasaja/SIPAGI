@extends('partial.main')

@section('body')
<div class="container">
    <h1>Edit Galeri Kegiatan</h1>
    <form action="{{ route('informasi.galeri.edit', $galeri->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul', $galeri->judul) }}" required>
            @error('judul')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar</label>
            <input type="file" name="gambar" id="gambar" class="form-control">
            <small>Biarkan kosong jika tidak ingin mengganti gambar.</small>
            @error('gambar')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control">{{ old('deskripsi', $galeri->deskripsi) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <div class="d-flex justify-content-end">
            <a href="{{ route('galeri.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
