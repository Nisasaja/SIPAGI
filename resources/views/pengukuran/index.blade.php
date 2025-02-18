@extends('partial.main')

{{-- Section untuk CSS khusus halaman ini --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('asset/css/pengukuran/ukur.css') }}">
@endsection

@section('body')
    <div class="container mt-5">
        <h1 class="mb-4">Data Status Gizi Balita</h1>
    
        <!-- Tombol Tambah Pengukuran -->
        @if(in_array(Auth::user()->role, ['Admin', 'Kader']))
        <a href="{{ route('pengukuran.create') }}" class="btn btn-primary mb-4">
            <i class="fa-solid fa-weight-scale me-2"></i>Tambah Data
        </a>
       @endif
       
        <!-- Form Pencarian dan Filter -->
        <form action="{{ route('pengukuran.index') }}" method="GET" class="bg-light p-3 rounded shadow-sm">
            <div class="row mb-3">
                <!-- Pilihan Tampilkan -->
                <div class="col-md-4 col-sm-6">
                    <div class="d-flex align-items-center">
                        <label for="perPage" class="me-2 mb-0">Tampilkan:</label>
                        <select id="perPage" name="perPage" class="form-select" onchange="this.form.submit()">
                            <option value="20" {{ request()->query('perPage') == 20 ? 'selected' : '' }}>20</option>
                            <option value="30" {{ request()->query('perPage') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request()->query('perPage') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request()->query('perPage') == 100 ? 'selected' : '' }}>100</option>
                            <option value="150" {{ request()->query('perPage') == 150 ? 'selected' : '' }}>150</option>
                        </select>
                    </div>
                </div>

                <!-- Pencarian -->
                <div class="col-md-4 col-sm-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request()->query('search') }}">
                </div>
            </div>

            <div class="row mb-3">
                <!-- Pilihan Bulan -->
                <div class="col-md-4 col-sm-6">
                    <label for="month" class="form-label">Pilih Bulan:</label>
                    <select name="month" id="month" class="form-select">
                        <option value="" {{ request()->query('month') == '' ? 'selected' : '' }}>Tampilkan</option>
                        <option value="01" {{ request()->query('month') == '01' ? 'selected' : '' }}>Januari</option>
                        <option value="02" {{ request()->query('month') == '02' ? 'selected' : '' }}>Februari</option>
                        <option value="03" {{ request()->query('month') == '03' ? 'selected' : '' }}>Maret</option>
                        <option value="04" {{ request()->query('month') == '04' ? 'selected' : '' }}>April</option>
                        <option value="05" {{ request()->query('month') == '05' ? 'selected' : '' }}>Mei</option>
                        <option value="06" {{ request()->query('month') == '06' ? 'selected' : '' }}>Juni</option>
                        <option value="07" {{ request()->query('month') == '07' ? 'selected' : '' }}>Juli</option>
                        <option value="08" {{ request()->query('month') == '08' ? 'selected' : '' }}>Agustus</option>
                        <option value="09" {{ request()->query('month') == '09' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ request()->query('month') == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ request()->query('month') == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ request()->query('month') == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>

                <!-- Pilihan Tahun -->
                <div class="col-md-4 col-sm-6">
                    <label for="year" class="form-label">Pilih Tahun:</label>
                    <select name="year" id="year" class="form-select">
                        <option value="" {{ request()->query('year') == '' ? 'selected' : '' }}>Tampilkan</option>
                        <option value="2023" {{ request()->query('year') == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ request()->query('year') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2025" {{ request()->query('year') == '2025' ? 'selected' : '' }}>2025</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Tombol Filter dan Reset -->
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('pengukuran.index') }}" class="btn btn-secondary">Reset</a>
                </div>

                <!-- Tombol Download CSV -->
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('pengukuran.download', request()->query()) }}" class="btn btn-success">
                        <i class="fa-solid fa-file-excel me-2"></i>Download CSV
                    </a>
                </div>
            </div>
        </form>

  
        <!-- Card untuk Tabel -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Anak</th>
                                <th>Desa</th>
                                <th>Tanggal Pengukuran</th>
                                <th>Berat Badan (kg)</th>
                                <th>Tinggi Badan (cm)</th>
                                <th>Status BB/U</th>
                                <th>Status TB/U</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengukuran as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->profile->nama_anak }}</td>
                                    <td>{{ $data->profile->alamat }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal_pengukuran)->format('d M Y') }}</td>
                                    <td>{{ number_format($data->berat_badan, 2) }}</td>
                                    <td>{{ number_format($data->tinggi_badan, 2) }}</td>
                                    <td>{{ $data->status_bb_u }}</td>
                                    <td>{{ $data->status_tb_u }}</td>
                                    <td>
                                        @if(in_array(Auth::user()->role, ['Admin', 'Kader']))
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('pengukuran.edit', $data->id) }}" class="me-3 text-decoration-none" aria-label="Edit">
                                                    <i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i>
                                                </a>
                                                <form action="{{ route('pengukuran.destroy', $data->id) }}" method="POST" class="mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link p-0" aria-label="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="fa-solid fa-trash" style="color: #fb2828; cursor: pointer;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Hanya dapat melihat</span>
                                        @endif
                                    </td>                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
    
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $pengukuran->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script> <!-- Pastikan plugin dimuat -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
            @endif
        
            // Inisialisasi Flatpickr dengan plugin monthSelect
            flatpickr("#month_year", {
                dateFormat: "Y-m", // Format tahun-bulan
                plugins: [new monthSelectPlugin({
                    shorthand: true, // Menampilkan bulan dengan singkatan
                    dateFormat: "Y-m", // Format yang digunakan
                    altFormat: "F Y" // Format alternatif yang ditampilkan
                })],
                onChange: function(selectedDates, dateStr, instance) {
                    // Set the month and year manually for the form
                    var [year, month] = dateStr.split('-');
                    var searchParams = new URLSearchParams(window.location.search);
                    searchParams.set('month', month);
                    searchParams.set('year', year);
                    window.location.search = searchParams.toString();
                }
            });
        });        
    </script>
@endsection

    <style>
        .form-bar {
            background-color: #ffc0c0; /* Latar belakang */
            padding: 10px 15px; /* Spasi di dalam form */
            border-radius: 8px; /* Sudut melengkung */
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px; /* Jarak antar elemen */
        }

        .form-bar .form-select, .form-bar .form-control {
            width: auto; /* Lebar sesuai konten */
            min-width: 150px; /* Lebar minimal */
        }

        .form-bar button, .form-bar a {
            white-space: nowrap; /* Menghindari teks terpotong */
        }
    </style>