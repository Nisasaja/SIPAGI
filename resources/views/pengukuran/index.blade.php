@extends('partial.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('asset/css/pengukuran/ukur.css') }}">
@endsection

@section('body')
<div class="container mt-5">
    <h1 class="mb-3 text-center">Data Status Gizi Balita</h1>

    <!-- Tombol Tambah Data -->
    @if(in_array(Auth::user()->role, ['Admin', 'Kader']))
    <a href="{{ route('pengukuran.create') }}" class="btn btn-primary mb-3">
        <i class="fa-solid fa-weight-scale me-2"></i>Tambah Data
    </a>
    @endif

    <!-- Form Pencarian dan Filter -->
    <form action="{{ route('pengukuran.index') }}" method="GET" class="bg-light p-3 rounded shadow-sm">
        <div class="row mb-3">
            <!-- Pilihan Tampilkan -->
            <div class="col-md-4 col-sm-6 mb-3">
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
            <div class="col-md-4 col-sm-6 mb-3">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request()->query('search') }}">
            </div>
        </div>

        <div class="row mb-3">
            <!-- Filter Bulan -->
            <div class="col-md-4 col-sm-6 mb-3">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <option value="">Pilih Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request()->query('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-4 col-sm-6 mb-3">
                <select name="year" class="form-select" onchange="this.form.submit()">
                    <option value="">Pilih Tahun</option>
                    @for ($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request()->query('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Filter Alamat -->
            <div class="col-md-4 col-sm-6 mb-3">
                <select name="alamat" class="form-select" onchange="this.form.submit()">
                    <option value="">Pilih Alamat</option>
                    @foreach($pengukuran->groupBy('profile.alamat') as $alamat => $dataPengukuran)
                        <option value="{{ $alamat }}" {{ request()->query('alamat') == $alamat ? 'selected' : '' }}>{{ $alamat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Tombol Filter dan Reset -->
            <div class="col-md-6 d-flex gap-2 mb-3">
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="{{ route('pengukuran.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- Tombol Download CSV -->
            @if(in_array(Auth::user()->role, ['Admin', 'Kader']))
            <div class="col-md-6 text-md-end mb-3">
                <form action="{{ route('pengukuran.download') }}" method="GET">
                    @csrf
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    <input type="hidden" name="month" value="{{ request()->query('month') }}">
                    <input type="hidden" name="year" value="{{ request()->query('year') }}">
                    <input type="hidden" name="alamat" value="{{ request()->query('alamat') }}">
                    <input type="hidden" name="perPage" value="{{ request()->query('perPage') }}">
                    <button type="submit" class="btn btn-success" aria-label="Download CSV">
                        <i class="fa-solid fa-file-excel me-2"></i>Download CSV
                    </button>
                </form>
            </div>
            @endif
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
                        @foreach($pengukuran as $data)
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
                                    <a href="{{ route('pengukuran.edit', $data->id) }}" class="me-2 text-decoration-none" aria-label="Edit">
                                        <i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i>
                                    </a>
                                    <form action="{{ route('pengukuran.destroy', $data->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" aria-label="Delete">
                                            <i class="fa-solid fa-trash" style="color: #fb2828; cursor: pointer;"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">Hanya dapat melihat</span>
                                @endif
                            </td>                                    
                        </tr>
                        @endforeach
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

<!-- Modal for Success Messages -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
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
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif
        });
    </script>

    <style>
        .nav-pills .nav-link {
            font-weight: bold;
            color: #007bff;
            margin: 0 5px;
            border-radius: 5px;
        }
        .nav-pills .nav-link:hover, .nav-pills .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            white-space: nowrap;
        }
        .table thead th {
            text-align: center;
        }
    </style>
@endsection
