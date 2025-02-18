@extends('partial.main')

@section('body')
<div class="container">
    <h1 class="mt-5">Edit Pengukuran</h1>
    <form action="{{ route('pengukuran.update', $pengukuran->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="tanggal_pengukuran" class="form-label">Tanggal Pengukuran</label>
            <input type="date" class="form-control" id="tanggal_pengukuran" name="tanggal_pengukuran" value="{{ $pengukuran->tanggal_pengukuran }}" required>
        </div>
        <div class="mb-3">
            <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
            <input type="number" step="0.01" class="form-control" id="berat_badan" name="berat_badan" value="{{ $pengukuran->berat_badan }}" required>
        </div>
        <div class="mb-3">
            <label for="tinggi_badan" class="form-label">Tinggi Badan (cm)</label>
            <input type="number" step="0.01" class="form-control" id="tinggi_badan" name="tinggi_badan" value="{{ $pengukuran->tinggi_badan }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
