@extends('partial.main')

@section('body')
<link rel="stylesheet" href="{{ asset('asset/css/modal.css') }}">
        <div class="content mt-0">
            <div class="container">
                <h1 class="text-center">Profil Balita</h1>
                
                <!-- Tombol Tambah Profil -->
                @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Kader')
                    <div class="mb-3">                       
                        <a class="btn btn-primary" href="{{ route('profiles.create') }}">
                            <i class="fa-solid fa-user me-2"></i>Tambah Profil
                        </a>
                    </div>
                @endif

                <!-- Form Filter -->
                <div class="filter-container">
                    <form action="{{ route('profiles.index') }}" method="GET" class="filter-form d-flex align-items-center justify-content-between flex-wrap p-3">
                        <div class="d-flex align-items-center gap-3">
                            <label for="perPage" class="mb-0">Tampilkan:</label>
                            <select name="perPage" class="form-select" style="width: 150px;" onchange="this.form.submit()">
                                <option value="10" {{ request()->query('perPage') == '10' ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request()->query('perPage') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request()->query('perPage') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request()->query('perPage') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <select name="desa" class="form-select" style="width: 200px;">
                                <option value="">Semua Desa</option>
                                @foreach ($listDesa as $desa)
                                    <option value="{{ $desa }}" {{ request()->query('desa') == $desa ? 'selected' : '' }}>
                                        {{ $desa }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="form-control" placeholder="Cari...." value="{{ request()->query('search') }}" style="width: 300px;">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                        <a href="{{ route('profile.download-pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fa-solid fa-file-pdf me-2"></i>Download PDF
                        </a>                                       
                    </form>
                </div>
            </div>
        </div>
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Anak</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Usia</th>
                                    <th>Asal Desa</th>
                                    <th>Status ASI</th>
                                    <th>Status Imunisasi</th>
                                    <th>BB Lahir</th>
                                    <th>TB Lahir</th>
                                    <th>Jamban</th>
                                    <th>Riwayat Kesehatan</th>
                                    <th>Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profiles as $profile)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $profile->nama_anak }}</td>
                                    <td>{{ $profile->jenis_kelamin }}</td>
                                    <td>{{ $profile->usia }} tahun</td>
                                    <td>{{ $profile->alamat }}</td>
                                    <td>{{ $profile->status_asi }}</td>
                                    <td>{{ $profile->status_imunisasi }}</td>
                                    <td>{{ number_format($profile->bb_lahir, 2) }}</td>
                                    <td>{{ number_format($profile->tb_lahir, 2) }}</td>
                                    <td>{{ $profile->kepemilikan_jamban }}</td>
                                    <td>{{ $profile->riwayat_kesehatan }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link p-0" style="color: #63E6BE; font-size: 16px;" data-bs-toggle="modal" data-bs-target="#profileModal{{ $profile->id }}">
                                            <i class="fa-solid fa-eye me-2" style="font-size: 18px;"></i> Lihat
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="profileModal{{ $profile->id }}" tabindex="-1" aria-labelledby="profileModalLabel{{ $profile->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="profileModalLabel{{ $profile->id }}">
                                                            <i class="fa-solid fa-user me-2"></i> Detail Profil: {{ $profile->nama_anak }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <div class="modal-body">
                                                        <!-- Data Orang Tua -->
                                                        <div class="mb-3 category-section parent-info">
                                                            <h6><i class="fa-solid fa-user me-2"></i> Data Orang Tua</h6>
                                                            <ul>
                                                                <li><strong>Nama Ibu:</strong> {{ $profile->nama_ibu }}</li>
                                                                <li><strong>Usia Ibu:</strong> {{ $profile->usia_ibu }} tahun</li>
                                                                <li><strong>Pendidikan Ibu:</strong> {{ $profile->pendidikan_ibu }}</li>
                                                                <li><strong>Nama Ayah:</strong> {{ $profile->nama_ayah }}</li>
                                                                <li><strong>Pendidikan Ayah:</strong> {{ $profile->pendidikan_ayah }}</li>
                                                                <li><strong>Pekerjaan Ayah:</strong> {{ $profile->pekerjaan_ayah }}</li>
                                                            </ul>
                                                        </div>
                                                        <!-- Data Anak -->
                                                        <div class="mb-3 category-section child-info">
                                                            <h6><i class="fa-solid fa-child me-2"></i> Data Anak</h6>
                                                            <ul>
                                                                <li><strong>Nama Anak:</strong> {{ $profile->nama_anak }}</li>
                                                                <li><strong>Jenis Kelamin:</strong> {{ $profile->jenis_kelamin }}</li>
                                                                <li><strong>Tanggal Lahir:</strong> {{ $profile->tanggal_lahir }}</li>
                                                                <li><strong>Asal Desa:</strong> {{ $profile->alamat }}</li>
                                                                <li><strong>Anak Ke:</strong> {{ $profile->anak_ke }}</li>
                                                                <li><strong>Status ASI:</strong> {{ $profile->status_asi }}</li>
                                                                <li><strong>Status Imunisasi:</strong> {{ $profile->status_imunisasi }}</li>
                                                                <li><strong>BB Lahir:</strong> {{ $profile->bb_lahir }} kg</li>
                                                                <li><strong>TB Lahir:</strong> {{ $profile->tb_lahir }} cm</li>
                                                            </ul>
                                                        </div>
                                                        <!-- Data Sanitasi dan Riwayat Kesehatan -->
                                                        <div class="mb-3 category-section sanitation-info">
                                                            <h6><i class="fa-solid fa-water me-2"></i> Data Sanitasi & Kesehatan</h6>
                                                            <ul>
                                                                <li><strong>Kepemilikan Jamban:</strong> {{ $profile->kepemilikan_jamban }}</li>
                                                                <li><strong>Luas Rumah:</strong> {{ $profile->luas_rumah }} m²</li>
                                                                <li><strong>Lantai Rumah:</strong> {{ $profile->lantai_rumah }}</li>
                                                                <li><strong>Jumlah Penghuni:</strong> {{ $profile->jml_penghuni }}</li>
                                                                <li><strong>Alat Masak:</strong> {{ $profile->alat_masak }}</li>
                                                                <li><strong>Sumber Air Bersih:</strong> {{ $profile->sumber_air }}</li>
                                                                <li><strong>Riwayat Kesehatan:</strong> {{ $profile->riwayat_kesehatan }}</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!-- Modal Footer -->
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fa-solid fa-xmark me-2"></i> Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Kader')
                                            <a class="fa-solid fa-pen-to-square me-2" style="color: #74C0FC;" href="{{ route('profiles.edit', $profile->id) }}"></a>
                                            
                                            <form action="{{ route('profiles.destroy', $profile->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="fa-solid fa-trash" style="border: none; background: none; color: #fb2828; cursor: pointer;" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus profil ini?')"></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Tampilkan Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $profiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ session('success') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM content loaded');
            @if (session('success'))
                console.log('Success message found');
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
            @endif
        });
</script>
@endsection
