@extends('partial.main')

@section('body')
<div class="container mt-5">
    <div class="card shadow-lg p-4 border-0">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Tambah Data Pengukuran</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Ada beberapa masalah dengan inputan Anda.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pengukuran.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="profile_id" class="form-label">Nama Anak</label>
                    <select name="profile_id" id="profile_id" class="form-control select2" required>
                        <option value="">Pilih Anak</option>
                        @foreach ($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->nama_anak }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal_pengukuran" class="form-label">Tanggal Pengukuran:</label>
                    <input type="date" name="tanggal_pengukuran" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="berat_badan" class="form-label">Berat Badan (kg):</label>
                    <input type="number" step="0.01" name="berat_badan" class="form-control" placeholder="Masukkan Berat Badan" required>
                </div>

                <div class="mb-3">
                    <label for="tinggi_badan" class="form-label">Tinggi Badan (cm):</label>
                    <input type="number" step="0.01" name="tinggi_badan" class="form-control" placeholder="Masukkan Tinggi Badan" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                    <a href="{{ route('pengukuran.index') }}" class="btn btn-secondary px-4">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#profile_id').select2({
                placeholder: 'Pilih Anak',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush

@endsection
