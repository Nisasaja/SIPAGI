<?php

namespace App\Http\Controllers;

use App\Models\Pengukuran;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Laracsv\Export; // Add this line

class PengukuranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedMonth = $request->input('month');

        // Definisikan array bulan
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        // Mengambil nilai jumlah data per halaman (default 20)
        $perPage = $request->input('perPage', 20);

        // Mengambil data pengukuran dengan filter
        $query = Pengukuran::with('profile');

        // Filter berdasarkan pencarian
        if ($search) {
            $query->whereHas('profile', function ($q) use ($search) {
                $q->where('nama_anak', 'like', '%' . $search . '%')
                ->orWhere('alamat', 'like', '%' . $search . '%')
                ->orWhere('status_bb_u', 'like', '%' . $search . '%')
                ->orWhere('status_tb_u', 'like', '%' . $search . '%')
                ->orWhere('berat_badan', 'like', '%' . $search . '%')
                ->orWhere('tinggi_badan', 'like', '%' . $search . '%')
                ->orWhere('tanggal_pengukuran', 'like', $search . '%');
            });
        }

        // Filter berdasarkan bulan
        if ($selectedMonth) {
            $query->whereMonth('tanggal_pengukuran', $selectedMonth);
        }

        // Filter berdasarkan tahun
        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('tanggal_pengukuran', $request->year);
        }

        // Pagination dengan jumlah data yang dipilih
        $pengukuran = $query->orderBy('tanggal_pengukuran', 'desc')->paginate($perPage);

        return view('pengukuran.index', compact('pengukuran', 'months', 'selectedMonth', 'perPage'), [
            'judul' => 'Pengukuran',
        ]);
    }

    public function searchPengukuran(Request $request)
    {
        // Mengambil input pencarian
        $searchTerm = $request->input('term');
        
        // Cari anak berdasarkan nama dengan LIKE
        $profiles = \App\Models\Profile::where('nama_anak', 'LIKE', "%{$searchTerm}%")
            ->select('id', 'nama_anak')
            ->limit(10) // Batasi 10 hasil
            ->get();
        
        // Kembalikan data dalam format JSON
        return response()->json($profiles);
    }

    public function download(Request $request)
{
    // Cek Hak Akses
    if (!in_array(Auth::user()->role, ['Admin', 'Kader', 'Manager'])) {
        return redirect()->route('landingpage')->with('error', 'Unauthorized access.');
    }

    // Query Data Pengukuran
    $pengukuran = Pengukuran::with('profile');

    // Filter Berdasarkan Nama Anak (Jika Ada)
    if ($request->has('search')) {
        $pengukuran->whereHas('profile', function ($query) use ($request) {
            $query->where('nama_anak', 'like', '%' . $request->search . '%');
        });
    }

    // Filter Berdasarkan Bulan
    if ($request->has('month') && !empty($request->month)) {
        $pengukuran->whereMonth('tanggal_pengukuran', $request->month);
    }

    // Filter Berdasarkan Tahun
    if ($request->has('year') && !empty($request->year)) {
        $pengukuran->whereYear('tanggal_pengukuran', $request->year);
    }

    // Ambil Data
    $data = $pengukuran->get();

    // Buat dan Unduh CSV
    $csvExporter = new Export();
    $csvExporter->build($data, [
        'profile.nama_anak' => 'Nama Anak',
        'profile.alamat' => 'Alamat',
        'tanggal_pengukuran' => function ($row) {
            return \Carbon\Carbon::parse($row->tanggal_pengukuran)->format('d-m-Y');
        },
        'berat_badan' => 'Berat Badan (kg)',
        'tinggi_badan' => 'Tinggi Badan (cm)',
        'status_bb_u' => 'Status BB/U',
        'status_tb_u' => 'Status TB/U',
    ])->download('data_pengukuran.csv');
}


    public function show($id)
    {
    $pengukuran = Pengukuran::with('profile')->findOrFail($id);
    return view('pengukuran.show', compact('pengukuran'), [
        'judul' => 'Detail Pengukuran'
    ]);
    }

    public function create()
    {
        if (!in_array(auth()->user()->role, ['Admin', 'Kader'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Mengambil hanya id dan nama_anak dari tabel Profile untuk optimasi
        $profiles = Profile::select('id', 'nama_anak')->get(); 
        
        // Mengirim data ke view 'pengukuran.create' dengan judul halaman
        return view('pengukuran.create', compact('profiles'))->with('judul', 'Tambah Data Pengukuran');
    }


    public function store(Request $request)
{
    $request->validate([
        'profile_id' => 'required|exists:profile,id',
        'tanggal_pengukuran' => 'required|date',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
    ]);

    // Ambil data profile termasuk tanggal lahir
    $profile = Profile::find($request->profile_id);

    // Menghitung status BB/U dan TB/U berdasarkan tanggal lahir dari profile
    $statusBBU = $this->calculateBBU(
        $request->berat_badan, 
        $profile->tanggal_lahir, 
        $request->tanggal_pengukuran, 
        $profile->jenis_kelamin);
    $statusTBU = $this->calculateTBU(
        $request->tinggi_badan, 
        $profile->tanggal_lahir, 
        $request->tanggal_pengukuran, 
        $profile->jenis_kelamin);

    Pengukuran::create([
        'profile_id' => $request->profile_id,
        'tanggal_pengukuran' => $request->tanggal_pengukuran,
        'berat_badan' => $request->berat_badan,
        'tinggi_badan' => $request->tinggi_badan,
        'status_bb_u' => $statusBBU,
        'status_tb_u' => $statusTBU,
    ]);

    return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil ditambahkan.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'tanggal_pengukuran' => 'required|date',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
    ]);

    $pengukuran = Pengukuran::findOrFail($id);
    $pengukuran->tanggal_pengukuran = $request->input('tanggal_pengukuran');
    $pengukuran->berat_badan = $request->input('berat_badan');
    $pengukuran->tinggi_badan = $request->input('tinggi_badan');

    // Menghitung usia dalam bulan
    $usia_bulan = $this->calculateUsiaBulan(
        $pengukuran->profile->tanggal_lahir, 
        $pengukuran->tanggal_pengukuran
    );

    // Update status BB/U dan TB/U
    $pengukuran->status_bb_u = 
    $this->calculateBBU(
        $pengukuran->berat_badan, 
        $pengukuran->profile->tanggal_lahir, 
        $pengukuran->tanggal_pengukuran, 
        $pengukuran->profile->jenis_kelamin
    );
    $pengukuran->status_tb_u = 
    $this->calculateTBU(
        $pengukuran->tinggi_badan, 
        $pengukuran->profile->tanggal_lahir, 
        $pengukuran->tanggal_pengukuran, 
        $pengukuran->profile->jenis_kelamin
    );

    $pengukuran->save();

    return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil diperbarui');
}

// Method untuk menampilkan form edit pengukuran
public function edit(Pengukuran $pengukuran)
{
    if (!in_array(auth()->user()->role, ['Admin', 'Kader'])) {
        abort(403, 'Anda tidak memiliki akses untuk menambah data.');
    }

    // Mengambil semua profil untuk dropdown
    $profiles = Profile::all();

    return view('pengukuran.edit', compact('pengukuran', 'profiles'), ['judul'=>'Edit pengukuran']);
}

private function calculateUsiaBulan($tanggal_lahir, $tanggal_pengukuran)
{
    $lahir = \Carbon\Carbon::parse($tanggal_lahir);
    $ukur = \Carbon\Carbon::parse($tanggal_pengukuran);
    return $lahir->diffInMonths($ukur);
}

// Fungsi untuk menghitung status BB/U (berat badan berdasarkan umur)
private function calculateBBU($berat_badan, $tanggal_lahir, $tanggal_pengukuran, $jenis_kelamin)
{
    $usia_bulan = $this->calculateUsiaBulan($tanggal_lahir, $tanggal_pengukuran);

    // Data tabel Z-score BB/U sesuai Permenkes 2020 untuk laki-laki
    $zScoreTableBBU_L = [
        0 => ['median' => 3.3, 'sd_minus_3' => 2.1, 'sd_minus_2' => 2.5, 'sd_minus_1' => 2.9, 'sd_plus_1' => 3.9, 'sd_plus_2' => 4.4, 'sd_plus_3' => 5.0],
        1 => ['median' => 4.5, 'sd_minus_3' => 2.9, 'sd_minus_2' => 3.4, 'sd_minus_1' => 3.9, 'sd_plus_1' => 5.0, 'sd_plus_2' => 5.6, 'sd_plus_3' => 6.2],
        2 => ['median' => 5.6, 'sd_minus_3' => 3.8, 'sd_minus_2' => 4.3, 'sd_minus_1' => 4.9, 'sd_plus_1' => 6.3, 'sd_plus_2' => 7.1, 'sd_plus_3' => 8.0],
        3 => ['median' => 6.4, 'sd_minus_3' => 4.4, 'sd_minus_2' => 5.0, 'sd_minus_1' => 5.7, 'sd_plus_1' => 7.2, 'sd_plus_2' => 8.0, 'sd_plus_3' => 9.0],
        4 => ['median' => 7.0, 'sd_minus_3' => 4.9, 'sd_minus_2' => 5.6, 'sd_minus_1' => 6.2, 'sd_plus_1' => 7.8, 'sd_plus_2' => 8.7, 'sd_plus_3' => 9.7],
        5 => ['median' => 7.5, 'sd_minus_3' => 5.3, 'sd_minus_2' => 6.0, 'sd_minus_1' => 6.7, 'sd_plus_1' => 8.4, 'sd_plus_2' => 9.3, 'sd_plus_3' => 10.4],
        6 => ['median' => 7.9, 'sd_minus_3' => 5.7, 'sd_minus_2' => 6.4, 'sd_minus_1' => 7.1, 'sd_plus_1' => 8.8, 'sd_plus_2' => 9.8, 'sd_plus_3' => 10.9],
        7 => ['median' => 8.3, 'sd_minus_3' => 5.9, 'sd_minus_2' => 6.7, 'sd_minus_1' => 7.4, 'sd_plus_1' => 9.2, 'sd_plus_2' => 10.3, 'sd_plus_3' => 11.4],
        8 => ['median' => 8.6, 'sd_minus_3' => 6.2, 'sd_minus_2' => 6.9, 'sd_minus_1' => 7.7, 'sd_plus_1' => 9.6, 'sd_plus_2' => 10.7, 'sd_plus_3' => 11.9],
        9 => ['median' => 8.9, 'sd_minus_3' => 6.4, 'sd_minus_2' => 7.1, 'sd_minus_1' => 8.0, 'sd_plus_1' => 9.9, 'sd_plus_2' => 11.0, 'sd_plus_3' => 12.3],
        10 => ['median' => 9.2, 'sd_minus_3' => 6.6, 'sd_minus_2' => 7.4, 'sd_minus_1' => 8.2, 'sd_plus_1' => 10.2, 'sd_plus_2' => 11.4, 'sd_plus_3' => 12.7],
        11 => ['median' => 9.4, 'sd_minus_3' => 6.8, 'sd_minus_2' => 7.6, 'sd_minus_1' => 8.4, 'sd_plus_1' => 10.5, 'sd_plus_2' => 11.7, 'sd_plus_3' => 13.0],
        12 => ['median' => 9.6, 'sd_minus_3' => 6.9, 'sd_minus_2' => 7.7, 'sd_minus_1' => 8.6, 'sd_plus_1' => 10.8, 'sd_plus_2' => 12.0, 'sd_plus_3' => 13.3],
        13 => ['median' => 9.9, 'sd_minus_3' => 7.1, 'sd_minus_2' => 7.9, 'sd_minus_1' => 8.8, 'sd_plus_1' => 11.0, 'sd_plus_2' => 12.3, 'sd_plus_3' => 13.7],
        14 => ['median' => 10.1, 'sd_minus_3' => 7.2, 'sd_minus_2' => 8.1, 'sd_minus_1' => 9.0, 'sd_plus_1' => 11.3, 'sd_plus_2' => 12.6, 'sd_plus_3' => 14.0],
        15 => ['median' => 10.3, 'sd_minus_3' => 7.4, 'sd_minus_2' => 8.3, 'sd_minus_1' => 9.2, 'sd_plus_1' => 11.5, 'sd_plus_2' => 12.8, 'sd_plus_3' => 14.3],
        16 => ['median' => 10.5, 'sd_minus_3' => 7.5, 'sd_minus_2' => 8.4, 'sd_minus_1' => 9.4, 'sd_plus_1' => 11.7, 'sd_plus_2' => 13.1, 'sd_plus_3' => 14.6],
        17 => ['median' => 10.7, 'sd_minus_3' => 7.7, 'sd_minus_2' => 8.6, 'sd_minus_1' => 9.6, 'sd_plus_1' => 12.0, 'sd_plus_2' => 13.4, 'sd_plus_3' => 14.9],
        18 => ['median' => 10.9, 'sd_minus_3' => 7.8, 'sd_minus_2' => 8.8, 'sd_minus_1' => 9.8, 'sd_plus_1' => 12.2, 'sd_plus_2' => 13.7, 'sd_plus_3' => 15.3],
        19 => ['median' => 11.1, 'sd_minus_3' => 8.0, 'sd_minus_2' => 8.9, 'sd_minus_1' => 10.0, 'sd_plus_1' => 12.5, 'sd_plus_2' => 13.9, 'sd_plus_3' => 15.6],
        20 => ['median' => 11.3, 'sd_minus_3' => 8.1, 'sd_minus_2' => 9.1, 'sd_minus_1' => 10.1, 'sd_plus_1' => 12.7, 'sd_plus_2' => 14.2, 'sd_plus_3' => 15.9],
        21 => ['median' => 11.5, 'sd_minus_3' => 8.2, 'sd_minus_2' => 9.2, 'sd_minus_1' => 10.3, 'sd_plus_1' => 12.9, 'sd_plus_2' => 14.5, 'sd_plus_3' => 16.2],
        22 => ['median' => 11.8, 'sd_minus_3' => 8.4, 'sd_minus_2' => 9.4, 'sd_minus_1' => 10.5, 'sd_plus_1' => 13.2, 'sd_plus_2' => 14.7, 'sd_plus_3' => 16.5],
        23 => ['median' => 12.0, 'sd_minus_3' => 8.5, 'sd_minus_2' => 9.5, 'sd_minus_1' => 10.7, 'sd_plus_1' => 13.4, 'sd_plus_2' => 15.0, 'sd_plus_3' => 16.8],
        24 => ['median' => 12.2, 'sd_minus_3' => 8.6, 'sd_minus_2' => 9.7, 'sd_minus_1' => 10.8, 'sd_plus_1' => 13.6, 'sd_plus_2' => 15.3, 'sd_plus_3' => 17.1],
        25 => ['median' => 12.4, 'sd_minus_3' => 8.8, 'sd_minus_2' => 9.8, 'sd_minus_1' => 11.0, 'sd_plus_1' => 13.9, 'sd_plus_2' => 15.5, 'sd_plus_3' => 17.5],
        26 => ['median' => 12.5, 'sd_minus_3' => 8.9, 'sd_minus_2' => 10.0, 'sd_minus_1' => 11.2, 'sd_plus_1' => 14.1, 'sd_plus_2' => 15.8, 'sd_plus_3' => 17.8],
        27 => ['median' => 12.7, 'sd_minus_3' => 9.0, 'sd_minus_2' => 10.1, 'sd_minus_1' => 11.3, 'sd_plus_1' => 14.3, 'sd_plus_2' => 16.1, 'sd_plus_3' => 18.1],
        28 => ['median' => 12.9, 'sd_minus_3' => 9.1, 'sd_minus_2' => 10.2, 'sd_minus_1' => 11.5, 'sd_plus_1' => 14.5, 'sd_plus_2' => 16.3, 'sd_plus_3' => 18.4],
        29 => ['median' => 13.1, 'sd_minus_3' => 9.2, 'sd_minus_2' => 10.4, 'sd_minus_1' => 11.7, 'sd_plus_1' => 14.8, 'sd_plus_2' => 16.6, 'sd_plus_3' => 18.7],
        30 => ['median' => 13.3, 'sd_minus_3' => 9.4, 'sd_minus_2' => 10.5, 'sd_minus_1' => 11.8, 'sd_plus_1' => 15.0, 'sd_plus_2' => 16.9, 'sd_plus_3' => 19.0],
        31 => ['median' => 13.5, 'sd_minus_3' => 9.5, 'sd_minus_2' => 10.7, 'sd_minus_1' => 12.0, 'sd_plus_1' => 15.2, 'sd_plus_2' => 17.1, 'sd_plus_3' => 19.3],
        32 => ['median' => 13.7, 'sd_minus_3' => 9.6, 'sd_minus_2' => 10.8, 'sd_minus_1' => 12.1, 'sd_plus_1' => 15.4, 'sd_plus_2' => 17.4, 'sd_plus_3' => 19.6],
        33 => ['median' => 13.8, 'sd_minus_3' => 9.7, 'sd_minus_2' => 10.9, 'sd_minus_1' => 12.3, 'sd_plus_1' => 15.6, 'sd_plus_2' => 17.6, 'sd_plus_3' => 19.9],
        34 => ['median' => 14.0, 'sd_minus_3' => 9.8, 'sd_minus_2' => 11.0, 'sd_minus_1' => 12.4, 'sd_plus_1' => 15.8, 'sd_plus_2' => 17.8, 'sd_plus_3' => 20.2],
        35 => ['median' => 14.2, 'sd_minus_3' => 9.9, 'sd_minus_2' => 11.2, 'sd_minus_1' => 12.6, 'sd_plus_1' => 16.0, 'sd_plus_2' => 18.1, 'sd_plus_3' => 20.4],
        36 => ['median' => 14.3, 'sd_minus_3' => 10.0, 'sd_minus_2' => 11.3, 'sd_minus_1' => 12.7, 'sd_plus_1' => 16.2, 'sd_plus_2' => 18.3, 'sd_plus_3' => 20.7],
        37 => ['median' => 14.5, 'sd_minus_3' => 10.1, 'sd_minus_2' => 11.4, 'sd_minus_1' => 12.9, 'sd_plus_1' => 16.4, 'sd_plus_2' => 18.6, 'sd_plus_3' => 21.0],
        38 => ['median' => 14.7, 'sd_minus_3' => 10.2, 'sd_minus_2' => 11.5, 'sd_minus_1' => 13.0, 'sd_plus_1' => 16.6, 'sd_plus_2' => 18.8, 'sd_plus_3' => 21.3],
        39 => ['median' => 14.8, 'sd_minus_3' => 10.3, 'sd_minus_2' => 11.6, 'sd_minus_1' => 13.1, 'sd_plus_1' => 16.8, 'sd_plus_2' => 19.0, 'sd_plus_3' => 21.6],
        40 => ['median' => 15.0, 'sd_minus_3' => 10.4, 'sd_minus_2' => 11.8, 'sd_minus_1' => 13.3, 'sd_plus_1' => 17.0, 'sd_plus_2' => 19.3, 'sd_plus_3' => 21.9],
        41 => ['median' => 15.2, 'sd_minus_3' => 10.5, 'sd_minus_2' => 11.9, 'sd_minus_1' => 13.4, 'sd_plus_1' => 17.2, 'sd_plus_2' => 19.5, 'sd_plus_3' => 22.1],
        42 => ['median' => 15.3, 'sd_minus_3' => 10.6, 'sd_minus_2' => 12.0, 'sd_minus_1' => 13.6, 'sd_plus_1' => 17.4, 'sd_plus_2' => 19.7, 'sd_plus_3' => 22.4],
        43 => ['median' => 15.5, 'sd_minus_3' => 10.7, 'sd_minus_2' => 12.1, 'sd_minus_1' => 13.7, 'sd_plus_1' => 17.6, 'sd_plus_2' => 20.0, 'sd_plus_3' => 22.7],
        44 => ['median' => 15.7, 'sd_minus_3' => 10.8, 'sd_minus_2' => 12.2, 'sd_minus_1' => 13.8, 'sd_plus_1' => 17.8, 'sd_plus_2' => 20.2, 'sd_plus_3' => 23.0],
        45 => ['median' => 15.8, 'sd_minus_3' => 10.9, 'sd_minus_2' => 12.4, 'sd_minus_1' => 14.0, 'sd_plus_1' => 18.0, 'sd_plus_2' => 20.5, 'sd_plus_3' => 23.3],
        46 => ['median' => 16.0, 'sd_minus_3' => 11.0, 'sd_minus_2' => 12.5, 'sd_minus_1' => 14.1, 'sd_plus_1' => 18.2, 'sd_plus_2' => 20.7, 'sd_plus_3' => 23.6],
        47 => ['median' => 16.2, 'sd_minus_3' => 11.1, 'sd_minus_2' => 12.6, 'sd_minus_1' => 14.3, 'sd_plus_1' => 18.4, 'sd_plus_2' => 20.9, 'sd_plus_3' => 23.9],
        48 => ['median' => 16.3, 'sd_minus_3' => 11.2, 'sd_minus_2' => 12.7, 'sd_minus_1' => 14.4, 'sd_plus_1' => 18.6, 'sd_plus_2' => 21.2, 'sd_plus_3' => 24.2],
        49 => ['median' => 16.5, 'sd_minus_3' => 11.3, 'sd_minus_2' => 12.8, 'sd_minus_1' => 14.5, 'sd_plus_1' => 18.8, 'sd_plus_2' => 21.4, 'sd_plus_3' => 24.5],
        50 => ['median' => 16.7, 'sd_minus_3' => 11.4, 'sd_minus_2' => 12.9, 'sd_minus_1' => 14.7, 'sd_plus_1' => 19.0, 'sd_plus_2' => 21.7, 'sd_plus_3' => 24.8],
        51 => ['median' => 16.8, 'sd_minus_3' => 11.5, 'sd_minus_2' => 13.1, 'sd_minus_1' => 14.8, 'sd_plus_1' => 19.2, 'sd_plus_2' => 21.9, 'sd_plus_3' => 25.1],
        52 => ['median' => 17.0, 'sd_minus_3' => 11.6, 'sd_minus_2' => 13.2, 'sd_minus_1' => 15.0, 'sd_plus_1' => 19.4, 'sd_plus_2' => 22.2, 'sd_plus_3' => 25.4],
        53 => ['median' => 17.2, 'sd_minus_3' => 11.7, 'sd_minus_2' => 13.3, 'sd_minus_1' => 15.1, 'sd_plus_1' => 19.6, 'sd_plus_2' => 22.4, 'sd_plus_3' => 25.7],
        54 => ['median' => 17.3, 'sd_minus_3' => 11.8, 'sd_minus_2' => 13.4, 'sd_minus_1' => 15.2, 'sd_plus_1' => 19.8, 'sd_plus_2' => 22.7, 'sd_plus_3' => 26.0],
        55 => ['median' => 17.5, 'sd_minus_3' => 11.9, 'sd_minus_2' => 13.5, 'sd_minus_1' => 15.4, 'sd_plus_1' => 20.0, 'sd_plus_2' => 22.9, 'sd_plus_3' => 26.3],
        56 => ['median' => 17.7, 'sd_minus_3' => 12.0, 'sd_minus_2' => 13.6, 'sd_minus_1' => 15.5, 'sd_plus_1' => 20.2, 'sd_plus_2' => 23.2, 'sd_plus_3' => 26.6],
        57 => ['median' => 17.8, 'sd_minus_3' => 12.1, 'sd_minus_2' => 13.7, 'sd_minus_1' => 15.6, 'sd_plus_1' => 20.4, 'sd_plus_2' => 23.4, 'sd_plus_3' => 26.9],
        58 => ['median' => 18.0, 'sd_minus_3' => 12.2, 'sd_minus_2' => 13.8, 'sd_minus_1' => 15.8, 'sd_plus_1' => 20.6, 'sd_plus_2' => 23.7, 'sd_plus_3' => 27.2],
        59 => ['median' => 18.2, 'sd_minus_3' => 12.3, 'sd_minus_2' => 14.0, 'sd_minus_1' => 15.9, 'sd_plus_1' => 20.8, 'sd_plus_2' => 23.9, 'sd_plus_3' => 27.6],
        60 => ['median' => 18.3, 'sd_minus_3' => 12.4, 'sd_minus_2' => 14.1, 'sd_minus_1' => 16.0, 'sd_plus_1' => 21.0, 'sd_plus_2' => 24.2, 'sd_plus_3' => 27.9],
        // Tambahkan data usia lainnya
    ];

    // Data tabel Z-score BB/U untuk perempuan sesuai Permenkes 2020
    $zScoreTableBBU_P = [
        0 => ['median' => 3.2, 'sd_minus_3' => 2.0, 'sd_minus_2' => 2.4, 'sd_minus_1' => 2.8, 'sd_plus_1' => 3.7, 'sd_plus_2' => 4.2, 'sd_plus_3' => 4.8],
        1 => ['median' => 4.2, 'sd_minus_3' => 2.7, 'sd_minus_2' => 3.2, 'sd_minus_1' => 3.6, 'sd_plus_1' => 4.8, 'sd_plus_2' => 5.5, 'sd_plus_3' => 6.2],
        2 => ['median' => 5.1, 'sd_minus_3' => 3.4, 'sd_minus_2' => 3.9, 'sd_minus_1' => 4.5, 'sd_plus_1' => 5.8, 'sd_plus_2' => 6.6, 'sd_plus_3' => 7.5],
        3 => ['median' => 5.8, 'sd_minus_3' => 4.0, 'sd_minus_2' => 4.5, 'sd_minus_1' => 5.2, 'sd_plus_1' => 6.6, 'sd_plus_2' => 7.5, 'sd_plus_3' => 8.5],
        4 => ['median' => 6.4, 'sd_minus_3' => 4.4, 'sd_minus_2' => 5.0, 'sd_minus_1' => 5.7, 'sd_plus_1' => 7.3, 'sd_plus_2' => 8.2, 'sd_plus_3' => 9.3],
        5 => ['median' => 6.9, 'sd_minus_3' => 4.8, 'sd_minus_2' => 5.4, 'sd_minus_1' => 6.1, 'sd_plus_1' => 7.8, 'sd_plus_2' => 8.8, 'sd_plus_3' => 10.0],
        6 => ['median' => 7.3, 'sd_minus_3' => 5.1, 'sd_minus_2' => 5.7, 'sd_minus_1' => 6.5, 'sd_plus_1' => 8.2, 'sd_plus_2' => 9.3, 'sd_plus_3' => 10.6],
        7 => ['median' => 7.6, 'sd_minus_3' => 5.3, 'sd_minus_2' => 6.0, 'sd_minus_1' => 6.8, 'sd_plus_1' => 8.6, 'sd_plus_2' => 9.8, 'sd_plus_3' => 11.1],
        8 => ['median' => 7.9, 'sd_minus_3' => 5.6, 'sd_minus_2' => 6.3, 'sd_minus_1' => 7.0, 'sd_plus_1' => 9.0, 'sd_plus_2' => 10.2, 'sd_plus_3' => 11.6],
        9 => ['median' => 8.2, 'sd_minus_3' => 5.8, 'sd_minus_2' => 6.5, 'sd_minus_1' => 7.3, 'sd_plus_1' => 9.3, 'sd_plus_2' => 10.5, 'sd_plus_3' => 12.0],
        10 => ['median' => 8.5, 'sd_minus_3' => 5.9, 'sd_minus_2' => 6.7, 'sd_minus_1' => 7.5, 'sd_plus_1' => 9.6, 'sd_plus_2' => 10.9, 'sd_plus_3' => 12.4],
        11 => ['median' => 8.7, 'sd_minus_3' => 6.1, 'sd_minus_2' => 6.9, 'sd_minus_1' => 7.7, 'sd_plus_1' => 9.9, 'sd_plus_2' => 11.2, 'sd_plus_3' => 12.8],
        12 => ['median' => 8.9, 'sd_minus_3' => 6.3, 'sd_minus_2' => 7.0, 'sd_minus_1' => 7.9, 'sd_plus_1' => 10.1, 'sd_plus_2' => 11.5, 'sd_plus_3' => 13.1],
        13 => ['median' => 9.2, 'sd_minus_3' => 6.4, 'sd_minus_2' => 7.2, 'sd_minus_1' => 8.1, 'sd_plus_1' => 10.4, 'sd_plus_2' => 11.8, 'sd_plus_3' => 13.5],
        14 => ['median' => 9.4, 'sd_minus_3' => 6.6, 'sd_minus_2' => 7.4, 'sd_minus_1' => 8.3, 'sd_plus_1' => 10.6, 'sd_plus_2' => 12.1, 'sd_plus_3' => 13.8],
        15 => ['median' => 9.6, 'sd_minus_3' => 6.7, 'sd_minus_2' => 7.6, 'sd_minus_1' => 8.5, 'sd_plus_1' => 10.9, 'sd_plus_2' => 12.4, 'sd_plus_3' => 14.1],
        16 => ['median' => 9.8, 'sd_minus_3' => 6.9, 'sd_minus_2' => 7.7, 'sd_minus_1' => 8.7, 'sd_plus_1' => 11.1, 'sd_plus_2' => 12.6, 'sd_plus_3' => 14.5],
        17 => ['median' => 10.0, 'sd_minus_3' => 7.0, 'sd_minus_2' => 7.9, 'sd_minus_1' => 8.9, 'sd_plus_1' => 11.4, 'sd_plus_2' => 12.9, 'sd_plus_3' => 14.8],
        18 => ['median' => 10.2, 'sd_minus_3' => 7.2, 'sd_minus_2' => 8.1, 'sd_minus_1' => 9.1, 'sd_plus_1' => 11.6, 'sd_plus_2' => 13.2, 'sd_plus_3' => 15.1],
        19 => ['median' => 10.4, 'sd_minus_3' => 7.3, 'sd_minus_2' => 8.2, 'sd_minus_1' => 9.2, 'sd_plus_1' => 11.8, 'sd_plus_2' => 13.5, 'sd_plus_3' => 15.4],
        20 => ['median' => 10.6, 'sd_minus_3' => 7.5, 'sd_minus_2' => 8.4, 'sd_minus_1' => 9.4, 'sd_plus_1' => 12.1, 'sd_plus_2' => 13.7, 'sd_plus_3' => 15.7],
        21 => ['median' => 10.9, 'sd_minus_3' => 7.6, 'sd_minus_2' => 8.6, 'sd_minus_1' => 9.6, 'sd_plus_1' => 12.3, 'sd_plus_2' => 14.0, 'sd_plus_3' => 16.0],
        22 => ['median' => 11.1, 'sd_minus_3' => 7.8, 'sd_minus_2' => 8.7, 'sd_minus_1' => 9.8, 'sd_plus_1' => 12.5, 'sd_plus_2' => 14.3, 'sd_plus_3' => 16.4],
        23 => ['median' => 11.3, 'sd_minus_3' => 7.9, 'sd_minus_2' => 8.9, 'sd_minus_1' => 10.0, 'sd_plus_1' => 12.8, 'sd_plus_2' => 14.6, 'sd_plus_3' => 16.7],
        24 => ['median' => 11.5, 'sd_minus_3' => 8.1, 'sd_minus_2' => 9.0, 'sd_minus_1' => 10.2, 'sd_plus_1' => 13.0, 'sd_plus_2' => 14.8, 'sd_plus_3' => 17.0],
        25 => ['median' => 11.7, 'sd_minus_3' => 8.2, 'sd_minus_2' => 9.2, 'sd_minus_1' => 10.3, 'sd_plus_1' => 13.3, 'sd_plus_2' => 15.1, 'sd_plus_3' => 17.3],
        26 => ['median' => 11.9, 'sd_minus_3' => 8.4, 'sd_minus_2' => 9.4, 'sd_minus_1' => 10.5, 'sd_plus_1' => 13.5, 'sd_plus_2' => 15.4, 'sd_plus_3' => 17.7],
        27 => ['median' => 12.1, 'sd_minus_3' => 8.5, 'sd_minus_2' => 9.5, 'sd_minus_1' => 10.7, 'sd_plus_1' => 13.7, 'sd_plus_2' => 15.7, 'sd_plus_3' => 18.0],
        28 => ['median' => 12.3, 'sd_minus_3' => 8.6, 'sd_minus_2' => 9.7, 'sd_minus_1' => 10.9, 'sd_plus_1' => 14.0, 'sd_plus_2' => 16.0, 'sd_plus_3' => 18.3],
        29 => ['median' => 12.5, 'sd_minus_3' => 8.8, 'sd_minus_2' => 9.8, 'sd_minus_1' => 11.1, 'sd_plus_1' => 14.2, 'sd_plus_2' => 16.2, 'sd_plus_3' => 18.7],
        30 => ['median' => 12.7, 'sd_minus_3' => 8.9, 'sd_minus_2' => 10.0, 'sd_minus_1' => 11.2, 'sd_plus_1' => 14.4, 'sd_plus_2' => 16.5, 'sd_plus_3' => 19.0],
        31 => ['median' => 12.9, 'sd_minus_3' => 9.0, 'sd_minus_2' => 10.1, 'sd_minus_1' => 11.4, 'sd_plus_1' => 14.7, 'sd_plus_2' => 16.8, 'sd_plus_3' => 19.3],
        32 => ['median' => 13.1, 'sd_minus_3' => 9.1, 'sd_minus_2' => 10.3, 'sd_minus_1' => 11.6, 'sd_plus_1' => 14.9, 'sd_plus_2' => 17.1, 'sd_plus_3' => 19.6],
        33 => ['median' => 13.3, 'sd_minus_3' => 9.3, 'sd_minus_2' => 10.4, 'sd_minus_1' => 11.7, 'sd_plus_1' => 15.1, 'sd_plus_2' => 17.3, 'sd_plus_3' => 20.0],
        34 => ['median' => 13.5, 'sd_minus_3' => 9.4, 'sd_minus_2' => 10.5, 'sd_minus_1' => 11.9, 'sd_plus_1' => 15.4, 'sd_plus_2' => 17.6, 'sd_plus_3' => 20.3],
        35 => ['median' => 13.7, 'sd_minus_3' => 9.5, 'sd_minus_2' => 10.7, 'sd_minus_1' => 12.0, 'sd_plus_1' => 15.6, 'sd_plus_2' => 17.9, 'sd_plus_3' => 20.6],
        36 => ['median' => 13.9, 'sd_minus_3' => 9.6, 'sd_minus_2' => 10.8, 'sd_minus_1' => 12.2, 'sd_plus_1' => 15.8, 'sd_plus_2' => 18.1, 'sd_plus_3' => 20.9],
        37 => ['median' => 14.0, 'sd_minus_3' => 9.7, 'sd_minus_2' => 10.9, 'sd_minus_1' => 12.4, 'sd_plus_1' => 16.0, 'sd_plus_2' => 18.4, 'sd_plus_3' => 21.3],
        38 => ['median' => 14.2, 'sd_minus_3' => 9.8, 'sd_minus_2' => 11.1, 'sd_minus_1' => 12.5, 'sd_plus_1' => 16.3, 'sd_plus_2' => 18.7, 'sd_plus_3' => 21.6],
        39 => ['median' => 14.4, 'sd_minus_3' => 9.9, 'sd_minus_2' => 11.2, 'sd_minus_1' => 12.7, 'sd_plus_1' => 16.5, 'sd_plus_2' => 19.0, 'sd_plus_3' => 22.0],
        40 => ['median' => 14.6, 'sd_minus_3' => 10.1, 'sd_minus_2' => 11.3, 'sd_minus_1' => 12.8, 'sd_plus_1' => 16.7, 'sd_plus_2' => 19.2, 'sd_plus_3' => 22.3],
        41 => ['median' => 14.8, 'sd_minus_3' => 10.2, 'sd_minus_2' => 11.5, 'sd_minus_1' => 13.0, 'sd_plus_1' => 16.9, 'sd_plus_2' => 19.5, 'sd_plus_3' => 22.7],
        42 => ['median' => 15.0, 'sd_minus_3' => 10.3, 'sd_minus_2' => 11.6, 'sd_minus_1' => 13.1, 'sd_plus_1' => 17.2, 'sd_plus_2' => 19.8, 'sd_plus_3' => 23.0],
        43 => ['median' => 15.2, 'sd_minus_3' => 10.4, 'sd_minus_2' => 11.7, 'sd_minus_1' => 13.3, 'sd_plus_1' => 17.4, 'sd_plus_2' => 20.1, 'sd_plus_3' => 23.4],
        44 => ['median' => 15.3, 'sd_minus_3' => 10.5, 'sd_minus_2' => 11.8, 'sd_minus_1' => 13.4, 'sd_plus_1' => 17.6, 'sd_plus_2' => 20.4, 'sd_plus_3' => 23.7],
        45 => ['median' => 15.5, 'sd_minus_3' => 10.6, 'sd_minus_2' => 12.0, 'sd_minus_1' => 13.6, 'sd_plus_1' => 17.8, 'sd_plus_2' => 20.7, 'sd_plus_3' => 24.1],
        46 => ['median' => 15.7, 'sd_minus_3' => 10.7, 'sd_minus_2' => 12.1, 'sd_minus_1' => 13.7, 'sd_plus_1' => 18.1, 'sd_plus_2' => 20.9, 'sd_plus_3' => 24.5],
        47 => ['median' => 15.9, 'sd_minus_3' => 10.8, 'sd_minus_2' => 12.2, 'sd_minus_1' => 13.9, 'sd_plus_1' => 18.3, 'sd_plus_2' => 21.2, 'sd_plus_3' => 24.8],
        48 => ['median' => 16.1, 'sd_minus_3' => 10.9, 'sd_minus_2' => 12.3, 'sd_minus_1' => 14.0, 'sd_plus_1' => 18.5, 'sd_plus_2' => 21.5, 'sd_plus_3' => 25.2],
        49 => ['median' => 16.3, 'sd_minus_3' => 11.0, 'sd_minus_2' => 12.4, 'sd_minus_1' => 14.2, 'sd_plus_1' => 18.8, 'sd_plus_2' => 21.8, 'sd_plus_3' => 25.5],
        50 => ['median' => 16.4, 'sd_minus_3' => 11.1, 'sd_minus_2' => 12.6, 'sd_minus_1' => 14.3, 'sd_plus_1' => 19.0, 'sd_plus_2' => 22.1, 'sd_plus_3' => 25.9],
        51 => ['median' => 16.6, 'sd_minus_3' => 11.2, 'sd_minus_2' => 12.7, 'sd_minus_1' => 14.5, 'sd_plus_1' => 19.2, 'sd_plus_2' => 22.4, 'sd_plus_3' => 26.3],
        52 => ['median' => 16.8, 'sd_minus_3' => 11.3, 'sd_minus_2' => 12.8, 'sd_minus_1' => 14.6, 'sd_plus_1' => 19.4, 'sd_plus_2' => 22.6, 'sd_plus_3' => 26.6],
        53 => ['median' => 17.0, 'sd_minus_3' => 11.4, 'sd_minus_2' => 12.9, 'sd_minus_1' => 14.8, 'sd_plus_1' => 19.7, 'sd_plus_2' => 22.9, 'sd_plus_3' => 27.0],
        54 => ['median' => 17.2, 'sd_minus_3' => 11.5, 'sd_minus_2' => 13.0, 'sd_minus_1' => 14.9, 'sd_plus_1' => 19.9, 'sd_plus_2' => 23.2, 'sd_plus_3' => 27.4],
        55 => ['median' => 17.3, 'sd_minus_3' => 11.6, 'sd_minus_2' => 13.2, 'sd_minus_1' => 15.1, 'sd_plus_1' => 20.1, 'sd_plus_2' => 23.5, 'sd_plus_3' => 27.7],
        56 => ['median' => 17.5, 'sd_minus_3' => 11.7, 'sd_minus_2' => 13.3, 'sd_minus_1' => 15.2, 'sd_plus_1' => 20.3, 'sd_plus_2' => 23.8, 'sd_plus_3' => 28.1],
        57 => ['median' => 17.7, 'sd_minus_3' => 11.8, 'sd_minus_2' => 13.4, 'sd_minus_1' => 15.3, 'sd_plus_1' => 20.6, 'sd_plus_2' => 24.1, 'sd_plus_3' => 28.5],
        58 => ['median' => 17.9, 'sd_minus_3' => 11.9, 'sd_minus_2' => 13.5, 'sd_minus_1' => 15.5, 'sd_plus_1' => 20.8, 'sd_plus_2' => 24.4, 'sd_plus_3' => 28.8],
        59 => ['median' => 18.0, 'sd_minus_3' => 12.0, 'sd_minus_2' => 13.6, 'sd_minus_1' => 15.6, 'sd_plus_1' => 21.0, 'sd_plus_2' => 24.6, 'sd_plus_3' => 29.2],
        60 => ['median' => 18.2, 'sd_minus_3' => 12.1, 'sd_minus_2' => 13.7, 'sd_minus_1' => 15.8, 'sd_plus_1' => 21.2, 'sd_plus_2' => 24.9, 'sd_plus_3' => 29.5],

    ];

    $zScoreTableBBU = $jenis_kelamin === "L" ? $zScoreTableBBU_L : $zScoreTableBBU_P;

    if (isset($zScoreTableBBU[$usia_bulan])) {
        $medianBB = $zScoreTableBBU[$usia_bulan]['median'];
        $sd_minus_3 = $zScoreTableBBU[$usia_bulan]['sd_minus_3'];
        $sd_minus_2 = $zScoreTableBBU[$usia_bulan]['sd_minus_2'];
        $sd_minus_1 = $zScoreTableBBU[$usia_bulan]['sd_minus_1'];
        $sd_plus_1 = $zScoreTableBBU[$usia_bulan]['sd_plus_1'];
        $sd_plus_2 = $zScoreTableBBU[$usia_bulan]['sd_plus_2'];
        $sd_plus_3 = $zScoreTableBBU[$usia_bulan]['sd_plus_3'];

        // Tentukan status berdasarkan Z-score
        if ($berat_badan < $sd_minus_3) {
            return 'Gizi Buruk';
        } elseif ($berat_badan >= $sd_minus_3 && $berat_badan < $sd_minus_2) {
            return 'Gizi Kurang';
        } elseif ($berat_badan >= $sd_minus_2 && $berat_badan <= $sd_plus_1) {
            return 'Normal';
        } elseif ($berat_badan > $sd_plus_1 && $berat_badan <= $sd_plus_2) {
            return 'Gizi Lebih';
        } else {
            return 'Obesitas';
        }
    }

    return 'Lulus';
}

// Fungsi untuk menghitung status TB/U (tinggi badan berdasarkan umur)
private function calculateTBU($tinggi_badan, $tanggal_lahir, $tanggal_pengukuran, $jenis_kelamin)
{
    $usia_bulan = $this->calculateUsiaBulan($tanggal_lahir, $tanggal_pengukuran);

    // Data tabel Z-score TB/U sesuai Permenkes 2020 untuk anak laki-laki
    $zScoreTableTBU_L = [
        0 => ['median' => 49.9, 'sd_minus_3' => 44.2, 'sd_minus_2' => 46.1, 'sd_minus_1' => 48.0, 'sd_plus_1' => 51.8, 'sd_plus_2' => 53.7, 'sd_plus_3' => 55.6],
        1 => ['median' => 54.7, 'sd_minus_3' => 49.8, 'sd_minus_2' => 51.8, 'sd_minus_1' => 52.8, 'sd_plus_1' => 57.6, 'sd_plus_2' => 58.6, 'sd_plus_3' => 60.6],
        2 => ['median' => 58.4, 'sd_minus_3' => 52.4, 'sd_minus_2' => 54.4, 'sd_minus_1' => 56.4, 'sd_plus_1' => 60.4, 'sd_plus_2' => 62.4, 'sd_plus_3' => 64.4],
        3 => ['median' => 61.4, 'sd_minus_3' => 55.3, 'sd_minus_2' => 57.3, 'sd_minus_1' => 59.4, 'sd_plus_1' => 63.5, 'sd_plus_2' => 65.5, 'sd_plus_3' => 67.6],
        4 => ['median' => 63.9, 'sd_minus_3' => 57.6, 'sd_minus_2' => 59.7, 'sd_minus_1' => 61.8, 'sd_plus_1' => 66.0, 'sd_plus_2' => 68.0, 'sd_plus_3' => 70.1],
        5 => ['median' => 65.9, 'sd_minus_3' => 59.6, 'sd_minus_2' => 61.7, 'sd_minus_1' => 63.8, 'sd_plus_1' => 68.0, 'sd_plus_2' => 70.1, 'sd_plus_3' => 72.2],
        6 => ['median' => 67.6, 'sd_minus_3' => 61.2, 'sd_minus_2' => 63.3, 'sd_minus_1' => 65.5, 'sd_plus_1' => 69.8, 'sd_plus_2' => 71.9, 'sd_plus_3' => 74.0],
        7 => ['median' => 69.2, 'sd_minus_3' => 62.7, 'sd_minus_2' => 64.8, 'sd_minus_1' => 67.0, 'sd_plus_1' => 71.3, 'sd_plus_2' => 73.5, 'sd_plus_3' => 75.7],
        8 => ['median' => 70.6, 'sd_minus_3' => 64.0, 'sd_minus_2' => 66.2, 'sd_minus_1' => 68.4, 'sd_plus_1' => 72.8, 'sd_plus_2' => 75.0, 'sd_plus_3' => 77.2],
        9 => ['median' => 72.0, 'sd_minus_3' => 65.2, 'sd_minus_2' => 67.5, 'sd_minus_1' => 69.7, 'sd_plus_1' => 74.2, 'sd_plus_2' => 76.5, 'sd_plus_3' => 78.7],
        10 => ['median' => 73.3, 'sd_minus_3' => 66.4, 'sd_minus_2' => 68.7, 'sd_minus_1' => 71.0, 'sd_plus_1' => 75.6, 'sd_plus_2' => 77.9, 'sd_plus_3' => 80.1],
        11 => ['median' => 74.5, 'sd_minus_3' => 67.6, 'sd_minus_2' => 69.9, 'sd_minus_1' => 72.2, 'sd_plus_1' => 76.9, 'sd_plus_2' => 79.2, 'sd_plus_3' => 81.5],
        12 => ['median' => 75.7, 'sd_minus_3' => 68.6, 'sd_minus_2' => 71.0, 'sd_minus_1' => 73.4, 'sd_plus_1' => 78.1, 'sd_plus_2' => 80.5, 'sd_plus_3' => 82.9],
        13 => ['median' => 76.9, 'sd_minus_3' => 69.6, 'sd_minus_2' => 72.1, 'sd_minus_1' => 74.5, 'sd_plus_1' => 79.3, 'sd_plus_2' => 81.8, 'sd_plus_3' => 84.2],
        14 => ['median' => 78.0, 'sd_minus_3' => 70.6, 'sd_minus_2' => 73.1, 'sd_minus_1' => 75.6, 'sd_plus_1' => 80.5, 'sd_plus_2' => 83.0, 'sd_plus_3' => 85.5],
        15 => ['median' => 79.1, 'sd_minus_3' => 71.6, 'sd_minus_2' => 74.1, 'sd_minus_1' => 76.6, 'sd_plus_1' => 81.7, 'sd_plus_2' => 84.2, 'sd_plus_3' => 86.7],
        16 => ['median' => 80.2, 'sd_minus_3' => 72.5, 'sd_minus_2' => 75.0, 'sd_minus_1' => 77.6, 'sd_plus_1' => 82.8, 'sd_plus_2' => 85.4, 'sd_plus_3' => 88.0],
        17 => ['median' => 81.2, 'sd_minus_3' => 73.3, 'sd_minus_2' => 76.0, 'sd_minus_1' => 78.6, 'sd_plus_1' => 83.9, 'sd_plus_2' => 86.5, 'sd_plus_3' => 89.2],
        18 => ['median' => 82.3, 'sd_minus_3' => 74.2, 'sd_minus_2' => 76.9, 'sd_minus_1' => 79.6, 'sd_plus_1' => 85.0, 'sd_plus_2' => 87.7, 'sd_plus_3' => 90.4],
        19 => ['median' => 83.2, 'sd_minus_3' => 75.0, 'sd_minus_2' => 77.7, 'sd_minus_1' => 80.5, 'sd_plus_1' => 86.0, 'sd_plus_2' => 88.8, 'sd_plus_3' => 91.5],
        20 => ['median' => 84.2, 'sd_minus_3' => 75.8, 'sd_minus_2' => 78.6, 'sd_minus_1' => 81.4, 'sd_plus_1' => 87.0, 'sd_plus_2' => 89.8, 'sd_plus_3' => 92.6],
        21 => ['median' => 85.1, 'sd_minus_3' => 76.5, 'sd_minus_2' => 79.4, 'sd_minus_1' => 82.3, 'sd_plus_1' => 88.0, 'sd_plus_2' => 90.9, 'sd_plus_3' => 93.8],
        22 => ['median' => 86.0, 'sd_minus_3' => 77.2, 'sd_minus_2' => 80.2, 'sd_minus_1' => 83.1, 'sd_plus_1' => 89.0, 'sd_plus_2' => 91.9, 'sd_plus_3' => 94.9],
        23 => ['median' => 86.9, 'sd_minus_3' => 78.0, 'sd_minus_2' => 81.0, 'sd_minus_1' => 83.9, 'sd_plus_1' => 89.9, 'sd_plus_2' => 92.9, 'sd_plus_3' => 95.9],
        24 => ['median' => 87.1, 'sd_minus_3' => 78.0, 'sd_minus_2' => 81.0, 'sd_minus_1' => 84.1, 'sd_plus_1' => 90.2, 'sd_plus_2' => 93.2, 'sd_plus_3' => 96.3],
        25 => ['median' => 88.0, 'sd_minus_3' => 78.6, 'sd_minus_2' => 81.7, 'sd_minus_1' => 84.9, 'sd_plus_1' => 91.1, 'sd_plus_2' => 94.2, 'sd_plus_3' => 97.3],
        26 => ['median' => 88.4, 'sd_minus_3' => 79.3, 'sd_minus_2' => 82.5, 'sd_minus_1' => 85.6, 'sd_plus_1' => 92.0, 'sd_plus_2' => 95.2, 'sd_plus_3' => 98.3],
        27 => ['median' => 89.6, 'sd_minus_3' => 79.9, 'sd_minus_2' => 83.1, 'sd_minus_1' => 86.4, 'sd_plus_1' => 92.9, 'sd_plus_2' => 96.1, 'sd_plus_3' => 99.3],
        28 => ['median' => 90.4, 'sd_minus_3' => 80.5, 'sd_minus_2' => 83.8, 'sd_minus_1' => 87.1, 'sd_plus_1' => 93.7, 'sd_plus_2' => 97.0, 'sd_plus_3' => 100.3],
        29 => ['median' => 91.2, 'sd_minus_3' => 81.1, 'sd_minus_2' => 84.5, 'sd_minus_1' => 87.8, 'sd_plus_1' => 94.5, 'sd_plus_2' => 97.9, 'sd_plus_3' => 101.2],
        30 => ['median' => 91.9, 'sd_minus_3' => 81.7, 'sd_minus_2' => 85.1, 'sd_minus_1' => 88.5, 'sd_plus_1' => 95.3, 'sd_plus_2' => 98.7, 'sd_plus_3' => 102.1],
        31 => ['median' => 92.7, 'sd_minus_3' => 82.3, 'sd_minus_2' => 85.7, 'sd_minus_1' => 89.2, 'sd_plus_1' => 96.1, 'sd_plus_2' => 99.6, 'sd_plus_3' => 103.0],
        32 => ['median' => 93.4, 'sd_minus_3' => 82.8, 'sd_minus_2' => 86.4, 'sd_minus_1' => 89.9, 'sd_plus_1' => 96.9, 'sd_plus_2' => 100.4, 'sd_plus_3' => 103.9],
        33 => ['median' => 94.1, 'sd_minus_3' => 83.4, 'sd_minus_2' => 86.9, 'sd_minus_1' => 90.5, 'sd_plus_1' => 97.6, 'sd_plus_2' => 101.2, 'sd_plus_3' => 104.8],
        34 => ['median' => 94.8, 'sd_minus_3' => 83.9, 'sd_minus_2' => 87.5, 'sd_minus_1' => 91.1, 'sd_plus_1' => 98.4, 'sd_plus_2' => 102.0, 'sd_plus_3' => 105.6],
        35 => ['median' => 95.4, 'sd_minus_3' => 84.4, 'sd_minus_2' => 88.1, 'sd_minus_1' => 91.8, 'sd_plus_1' => 99.1, 'sd_plus_2' => 102.7, 'sd_plus_3' => 106.4],
        36 => ['median' => 96.1, 'sd_minus_3' => 85.0, 'sd_minus_2' => 88.7, 'sd_minus_1' => 92.4, 'sd_plus_1' => 99.8, 'sd_plus_2' => 103.5, 'sd_plus_3' => 107.2],
        37 => ['median' => 96.7, 'sd_minus_3' => 85.5, 'sd_minus_2' => 89.2, 'sd_minus_1' => 93.0, 'sd_plus_1' => 100.5, 'sd_plus_2' => 104.2, 'sd_plus_3' => 108.0],
        38 => ['median' => 97.4, 'sd_minus_3' => 86.0, 'sd_minus_2' => 89.8, 'sd_minus_1' => 93.6, 'sd_plus_1' => 101.2, 'sd_plus_2' => 105.0, 'sd_plus_3' => 108.9],
        39 => ['median' => 98.0, 'sd_minus_3' => 86.5, 'sd_minus_2' => 90.3, 'sd_minus_1' => 94.2, 'sd_plus_1' => 101.8, 'sd_plus_2' => 105.7, 'sd_plus_3' => 109.5],
        40 => ['median' => 98.6, 'sd_minus_3' => 87.0, 'sd_minus_2' => 90.9, 'sd_minus_1' => 94.7, 'sd_plus_1' => 102.5, 'sd_plus_2' => 106.4, 'sd_plus_3' => 110.3],
        41 => ['median' => 99.2, 'sd_minus_3' => 87.5, 'sd_minus_2' => 91.4, 'sd_minus_1' => 95.3, 'sd_plus_1' => 103.2, 'sd_plus_2' => 107.1, 'sd_plus_3' => 111.0],
        42 => ['median' => 99.9, 'sd_minus_3' => 88.0, 'sd_minus_2' => 91.9, 'sd_minus_1' => 95.9, 'sd_plus_1' => 103.8, 'sd_plus_2' => 107.8, 'sd_plus_3' => 111.7],
        43 => ['median' => 100.4, 'sd_minus_3' => 88.4, 'sd_minus_2' => 92.4, 'sd_minus_1' => 96.4, 'sd_plus_1' => 104.5, 'sd_plus_2' => 108.5, 'sd_plus_3' => 112.5],
        44 => ['median' => 101.0, 'sd_minus_3' => 88.9, 'sd_minus_2' => 93.0, 'sd_minus_1' => 97.0, 'sd_plus_1' => 105.1, 'sd_plus_2' => 109.1, 'sd_plus_3' => 113.2],
        45 => ['median' => 101.6, 'sd_minus_3' => 89.4, 'sd_minus_2' => 93.5, 'sd_minus_1' => 97.5, 'sd_plus_1' => 105.7, 'sd_plus_2' => 109.8, 'sd_plus_3' => 113.9],
        46 => ['median' => 102.2, 'sd_minus_3' => 89.8, 'sd_minus_2' => 94.0, 'sd_minus_1' => 98.1, 'sd_plus_1' => 106.3, 'sd_plus_2' => 110.4, 'sd_plus_3' => 114.6],
        47 => ['median' => 102.8, 'sd_minus_3' => 90.3, 'sd_minus_2' => 94.4, 'sd_minus_1' => 98.6, 'sd_plus_1' => 106.9, 'sd_plus_2' => 111.1, 'sd_plus_3' => 115.2],
        48 => ['median' => 103.3, 'sd_minus_3' => 90.7, 'sd_minus_2' => 94.9, 'sd_minus_1' => 99.1, 'sd_plus_1' => 107.5, 'sd_plus_2' => 111.7, 'sd_plus_3' => 115.9],
        49 => ['median' => 103.9, 'sd_minus_3' => 91.2, 'sd_minus_2' => 95.4, 'sd_minus_1' => 99.7, 'sd_plus_1' => 108.1, 'sd_plus_2' => 112.4, 'sd_plus_3' => 116.6],
        50 => ['median' => 104.4, 'sd_minus_3' => 91.6, 'sd_minus_2' => 95.9, 'sd_minus_1' => 100.2, 'sd_plus_1' => 108.7, 'sd_plus_2' => 113.0, 'sd_plus_3' => 117.3],
        51 => ['median' => 105.0, 'sd_minus_3' => 92.1, 'sd_minus_2' => 96.4, 'sd_minus_1' => 100.7, 'sd_plus_1' => 109.3, 'sd_plus_2' => 113.6, 'sd_plus_3' => 117.9],
        52 => ['median' => 105.6, 'sd_minus_3' => 92.5, 'sd_minus_2' => 96.9, 'sd_minus_1' => 101.2, 'sd_plus_1' => 109.9, 'sd_plus_2' => 114.2, 'sd_plus_3' => 118.6],
        53 => ['median' => 106.1, 'sd_minus_3' => 93.0, 'sd_minus_2' => 97.4, 'sd_minus_1' => 101.7, 'sd_plus_1' => 110.5, 'sd_plus_2' => 114.9, 'sd_plus_3' => 119.2],
        54 => ['median' => 106.7, 'sd_minus_3' => 93.4, 'sd_minus_2' => 97.8, 'sd_minus_1' => 102.3, 'sd_plus_1' => 111.1, 'sd_plus_2' => 115.5, 'sd_plus_3' => 119.9],
        55 => ['median' => 107.2, 'sd_minus_3' => 93.9, 'sd_minus_2' => 98.3, 'sd_minus_1' => 102.8, 'sd_plus_1' => 111.7, 'sd_plus_2' => 116.1, 'sd_plus_3' => 120.6],
        56 => ['median' => 107.8, 'sd_minus_3' => 94.3, 'sd_minus_2' => 98.8, 'sd_minus_1' => 103.3, 'sd_plus_1' => 112.3, 'sd_plus_2' => 116.7, 'sd_plus_3' => 121.2],
        57 => ['median' => 108.3, 'sd_minus_3' => 94.7, 'sd_minus_2' => 99.3, 'sd_minus_1' => 103.8, 'sd_plus_1' => 112.8, 'sd_plus_2' => 117.4, 'sd_plus_3' => 121.9],
        58 => ['median' => 108.9, 'sd_minus_3' => 95.2, 'sd_minus_2' => 99.7, 'sd_minus_1' => 104.3, 'sd_plus_1' => 113.4, 'sd_plus_2' => 118.0, 'sd_plus_3' => 122.6],
        59 => ['median' => 109.4, 'sd_minus_3' => 95.6, 'sd_minus_2' => 100.2, 'sd_minus_1' => 104.8, 'sd_plus_1' => 114.0, 'sd_plus_2' => 118.6, 'sd_plus_3' => 123.2],
        60 => ['median' => 110.0, 'sd_minus_3' => 96.1, 'sd_minus_2' => 100.7, 'sd_minus_1' => 105.3, 'sd_plus_1' => 114.6, 'sd_plus_2' => 119.2, 'sd_plus_3' => 123.9],
        // Tambahkan data usia lainnya
    ];
    $zScoreTableTBU_P = [
        0 => ['median' => 49.1, 'sd_minus_3' => 43.6, 'sd_minus_2' => 45.4, 'sd_minus_1' => 47.3, 'sd_plus_1' => 51.0, 'sd_plus_2' => 52.9, 'sd_plus_3' => 54.7],
        1 => ['median' => 53.7, 'sd_minus_3' => 47.8, 'sd_minus_2' => 49.8, 'sd_minus_1' => 51.7, 'sd_plus_1' => 55.6, 'sd_plus_2' => 57.6, 'sd_plus_3' => 59.5],
        2 => ['median' => 57.1, 'sd_minus_3' => 51.0, 'sd_minus_2' => 53.0, 'sd_minus_1' => 55.0, 'sd_plus_1' => 59.1, 'sd_plus_2' => 61.1, 'sd_plus_3' => 63.5],
        3 => ['median' => 59.8, 'sd_minus_3' => 53.5, 'sd_minus_2' => 55.6, 'sd_minus_1' => 57.7, 'sd_plus_1' => 61.9, 'sd_plus_2' => 64.0, 'sd_plus_3' => 66.1],
        4 => ['median' => 62.1, 'sd_minus_3' => 55.6, 'sd_minus_2' => 57.8, 'sd_minus_1' => 59.9, 'sd_plus_1' => 64.3, 'sd_plus_2' => 66.4, 'sd_plus_3' => 68.6],
        5 => ['median' => 64.0, 'sd_minus_3' => 57.4, 'sd_minus_2' => 59.6, 'sd_minus_1' => 61.8, 'sd_plus_1' => 66.2, 'sd_plus_2' => 68.5, 'sd_plus_3' => 70.7],
        6 => ['median' => 65.7, 'sd_minus_3' => 58.9, 'sd_minus_2' => 61.2, 'sd_minus_1' => 63.5, 'sd_plus_1' => 68.0, 'sd_plus_2' => 70.3, 'sd_plus_3' => 72.5],
        7 => ['median' => 67.3, 'sd_minus_3' => 60.3, 'sd_minus_2' => 62.7, 'sd_minus_1' => 65.0, 'sd_plus_1' => 69.6, 'sd_plus_2' => 71.9, 'sd_plus_3' => 74.2],
        8 => ['median' => 68.7, 'sd_minus_3' => 61.7, 'sd_minus_2' => 64.0, 'sd_minus_1' => 66.4, 'sd_plus_1' => 71.1, 'sd_plus_2' => 73.5, 'sd_plus_3' => 75.8],
        9 => ['median' => 70.1, 'sd_minus_3' => 62.9, 'sd_minus_2' => 65.3, 'sd_minus_1' => 67.7, 'sd_plus_1' => 72.6, 'sd_plus_2' => 75.0, 'sd_plus_3' => 77.4],
        10 => ['median' => 71.5, 'sd_minus_3' => 64.1, 'sd_minus_2' => 66.5, 'sd_minus_1' => 69.0, 'sd_plus_1' => 73.9, 'sd_plus_2' => 76.4, 'sd_plus_3' => 78.9],
        11 => ['median' => 72.8, 'sd_minus_3' => 65.2, 'sd_minus_2' => 67.7, 'sd_minus_1' => 70.3, 'sd_plus_1' => 75.3, 'sd_plus_2' => 77.8, 'sd_plus_3' => 80.3],
        12 => ['median' => 74.0, 'sd_minus_3' => 66.3, 'sd_minus_2' => 68.9, 'sd_minus_1' => 71.4, 'sd_plus_1' => 76.6, 'sd_plus_2' => 79.2, 'sd_plus_3' => 81.7],
        13 => ['median' => 75.2, 'sd_minus_3' => 67.3, 'sd_minus_2' => 70.0, 'sd_minus_1' => 72.6, 'sd_plus_1' => 77.8, 'sd_plus_2' => 80.5, 'sd_plus_3' => 83.1],
        14 => ['median' => 76.4, 'sd_minus_3' => 68.3, 'sd_minus_2' => 71.0, 'sd_minus_1' => 73.7, 'sd_plus_1' => 79.1, 'sd_plus_2' => 81.7, 'sd_plus_3' => 84.4],
        15 => ['median' => 77.5, 'sd_minus_3' => 69.3, 'sd_minus_2' => 72.0, 'sd_minus_1' => 74.8, 'sd_plus_1' => 80.2, 'sd_plus_2' => 83.0, 'sd_plus_3' => 85.7],
        16 => ['median' => 78.6, 'sd_minus_3' => 70.2, 'sd_minus_2' => 73.0, 'sd_minus_1' => 75.8, 'sd_plus_1' => 81.4, 'sd_plus_2' => 84.2, 'sd_plus_3' => 87.0],
        17 => ['median' => 79.7, 'sd_minus_3' => 71.0, 'sd_minus_2' => 74.0, 'sd_minus_1' => 76.8, 'sd_plus_1' => 82.5, 'sd_plus_2' => 85.4, 'sd_plus_3' => 88.2],
        18 => ['median' => 80.7, 'sd_minus_3' => 72.0, 'sd_minus_2' => 74.9, 'sd_minus_1' => 77.8, 'sd_plus_1' => 83.6, 'sd_plus_2' => 86.5, 'sd_plus_3' => 89.4],
        19 => ['median' => 81.7, 'sd_minus_3' => 72.8, 'sd_minus_2' => 75.8, 'sd_minus_1' => 78.8, 'sd_plus_1' => 84.7, 'sd_plus_2' => 87.6, 'sd_plus_3' => 90.6],
        20 => ['median' => 82.7, 'sd_minus_3' => 73.7, 'sd_minus_2' => 76.7, 'sd_minus_1' => 79.7, 'sd_plus_1' => 85.7, 'sd_plus_2' => 88.7, 'sd_plus_3' => 91.7],
        21 => ['median' => 83.7, 'sd_minus_3' => 74.5, 'sd_minus_2' => 77.5, 'sd_minus_1' => 80.6, 'sd_plus_1' => 86.7, 'sd_plus_2' => 89.8, 'sd_plus_3' => 92.9],
        22 => ['median' => 84.6, 'sd_minus_3' => 75.2, 'sd_minus_2' => 78.4, 'sd_minus_1' => 81.5, 'sd_plus_1' => 87.7, 'sd_plus_2' => 90.8, 'sd_plus_3' => 94.0],
        23 => ['median' => 85.5, 'sd_minus_3' => 76.0, 'sd_minus_2' => 79.2, 'sd_minus_1' => 82.3, 'sd_plus_1' => 88.7, 'sd_plus_2' => 91.9, 'sd_plus_3' => 95.0],
        24 => ['median' => 85.7, 'sd_minus_3' => 76.7, 'sd_minus_2' => 79.3, 'sd_minus_1' => 82.5, 'sd_plus_1' => 88.9, 'sd_plus_2' => 92.2, 'sd_plus_3' => 95.4],
        25 => ['median' => 86.6, 'sd_minus_3' => 76.8, 'sd_minus_2' => 80.0, 'sd_minus_1' => 83.3, 'sd_plus_1' => 89.9, 'sd_plus_2' => 93.1, 'sd_plus_3' => 96.4],
        26 => ['median' => 87.4, 'sd_minus_3' => 77.5, 'sd_minus_2' => 80.8, 'sd_minus_1' => 84.1, 'sd_plus_1' => 90.8, 'sd_plus_2' => 94.1, 'sd_plus_3' => 97.4],
        27 => ['median' => 88.3, 'sd_minus_3' => 78.1, 'sd_minus_2' => 81.5, 'sd_minus_1' => 84.9, 'sd_plus_1' => 91.7, 'sd_plus_2' => 95.0, 'sd_plus_3' => 98.4],
        28 => ['median' => 89.1, 'sd_minus_3' => 78.8, 'sd_minus_2' => 82.2, 'sd_minus_1' => 85.7, 'sd_plus_1' => 92.5, 'sd_plus_2' => 96.0, 'sd_plus_3' => 99.4],
        29 => ['median' => 89.9, 'sd_minus_3' => 79.5, 'sd_minus_2' => 82.9, 'sd_minus_1' => 86.4, 'sd_plus_1' => 93.4, 'sd_plus_2' => 96.9, 'sd_plus_3' => 100.3],
        30 => ['median' => 90.7, 'sd_minus_3' => 80.1, 'sd_minus_2' => 83.6, 'sd_minus_1' => 87.1, 'sd_plus_1' => 94.2, 'sd_plus_2' => 97.7, 'sd_plus_3' => 101.3],
        31 => ['median' => 91.4, 'sd_minus_3' => 80.7, 'sd_minus_2' => 84.3, 'sd_minus_1' => 87.9, 'sd_plus_1' => 95.0, 'sd_plus_2' => 98.6, 'sd_plus_3' => 102.2],
        32 => ['median' => 92.2, 'sd_minus_3' => 81.3, 'sd_minus_2' => 84.9, 'sd_minus_1' => 88.6, 'sd_plus_1' => 95.8, 'sd_plus_2' => 99.4, 'sd_plus_3' => 103.1],
        33 => ['median' => 92.9, 'sd_minus_3' => 81.9, 'sd_minus_2' => 85.6, 'sd_minus_1' => 89.3, 'sd_plus_1' => 96.6, 'sd_plus_2' => 100.3, 'sd_plus_3' => 103.9],
        34 => ['median' => 93.6, 'sd_minus_3' => 82.5, 'sd_minus_2' => 86.2, 'sd_minus_1' => 89.9, 'sd_plus_1' => 97.4, 'sd_plus_2' => 101.1, 'sd_plus_3' => 104.8],
        35 => ['median' => 94.4, 'sd_minus_3' => 83.1, 'sd_minus_2' => 86.8, 'sd_minus_1' => 90.6, 'sd_plus_1' => 98.1, 'sd_plus_2' => 101.9, 'sd_plus_3' => 105.6],
        36 => ['median' => 95.1, 'sd_minus_3' => 83.6, 'sd_minus_2' => 87.4, 'sd_minus_1' => 91.2, 'sd_plus_1' => 98.9, 'sd_plus_2' => 102.7, 'sd_plus_3' => 106.5],
        37 => ['median' => 95.7, 'sd_minus_3' => 84.2, 'sd_minus_2' => 88.0, 'sd_minus_1' => 91.9, 'sd_plus_1' => 99.6, 'sd_plus_2' => 103.4, 'sd_plus_3' => 107.3],
        38 => ['median' => 96.4, 'sd_minus_3' => 84.7, 'sd_minus_2' => 88.6, 'sd_minus_1' => 92.5, 'sd_plus_1' => 100.3, 'sd_plus_2' => 104.2, 'sd_plus_3' => 108.1],
        39 => ['median' => 97.1, 'sd_minus_3' => 85.3, 'sd_minus_2' => 89.2, 'sd_minus_1' => 93.1, 'sd_plus_1' => 101.0, 'sd_plus_2' => 105.0, 'sd_plus_3' => 108.9],
        40 => ['median' => 97.7, 'sd_minus_3' => 85.8, 'sd_minus_2' => 89.8, 'sd_minus_1' => 93.8, 'sd_plus_1' => 101.7, 'sd_plus_2' => 105.7, 'sd_plus_3' => 109.7],
        41 => ['median' => 98.4, 'sd_minus_3' => 86.3, 'sd_minus_2' => 90.4, 'sd_minus_1' => 94.4, 'sd_plus_1' => 102.4, 'sd_plus_2' => 106.4, 'sd_plus_3' => 110.5],
        42 => ['median' => 99.0, 'sd_minus_3' => 86.8, 'sd_minus_2' => 90.9, 'sd_minus_1' => 95.0, 'sd_plus_1' => 103.1, 'sd_plus_2' => 107.2, 'sd_plus_3' => 111.2],
        43 => ['median' => 99.7, 'sd_minus_3' => 87.4, 'sd_minus_2' => 91.5, 'sd_minus_1' => 95.6, 'sd_plus_1' => 103.8, 'sd_plus_2' => 107.9, 'sd_plus_3' => 112.0],
        44 => ['median' => 100.3, 'sd_minus_3' => 87.9, 'sd_minus_2' => 92.0, 'sd_minus_1' => 96.2, 'sd_plus_1' => 104.5, 'sd_plus_2' => 108.6, 'sd_plus_3' => 112.7],
        45 => ['median' => 100.9, 'sd_minus_3' => 88.4, 'sd_minus_2' => 92.5, 'sd_minus_1' => 96.7, 'sd_plus_1' => 105.1, 'sd_plus_2' => 109.3, 'sd_plus_3' => 113.5],
        46 => ['median' => 101.5, 'sd_minus_3' => 88.9, 'sd_minus_2' => 93.1, 'sd_minus_1' => 97.3, 'sd_plus_1' => 105.8, 'sd_plus_2' => 110.7, 'sd_plus_3' => 114.2],
        47 => ['median' => 102.1, 'sd_minus_3' => 89.3, 'sd_minus_2' => 93.6, 'sd_minus_1' => 97.9, 'sd_plus_1' => 106.4, 'sd_plus_2' => 111.3, 'sd_plus_3' => 114.9],
        48 => ['median' => 102.7, 'sd_minus_3' => 89.8, 'sd_minus_2' => 94.1, 'sd_minus_1' => 98.4, 'sd_plus_1' => 107.0, 'sd_plus_2' => 112.0, 'sd_plus_3' => 115.7],
        49 => ['median' => 103.3, 'sd_minus_3' => 90.3, 'sd_minus_2' => 94.6, 'sd_minus_1' => 99.0, 'sd_plus_1' => 107.7, 'sd_plus_2' => 112.7, 'sd_plus_3' => 116.4],
        50 => ['median' => 103.9, 'sd_minus_3' => 90.7, 'sd_minus_2' => 95.1, 'sd_minus_1' => 99.5, 'sd_plus_1' => 108.3, 'sd_plus_2' => 113.3, 'sd_plus_3' => 117.1],
        51 => ['median' => 104.5, 'sd_minus_3' => 91.2, 'sd_minus_2' => 95.6, 'sd_minus_1' => 100.1, 'sd_plus_1' => 108.9, 'sd_plus_2' => 114.0, 'sd_plus_3' => 117.1],
        52 => ['median' => 105.0, 'sd_minus_3' => 91.7, 'sd_minus_2' => 96.1, 'sd_minus_1' => 100.6, 'sd_plus_1' => 109.5, 'sd_plus_2' => 114.6, 'sd_plus_3' => 117.1],
        53 => ['median' => 105.6, 'sd_minus_3' => 92.1, 'sd_minus_2' => 96.6, 'sd_minus_1' => 101.1, 'sd_plus_1' => 110.1, 'sd_plus_2' => 115.2, 'sd_plus_3' => 117.1],
        54 => ['median' => 106.2, 'sd_minus_3' => 92.6, 'sd_minus_2' => 97.1, 'sd_minus_1' => 101.6, 'sd_plus_1' => 110.7, 'sd_plus_2' => 115.9, 'sd_plus_3' => 117.1],
        55 => ['median' => 106.7, 'sd_minus_3' => 93.0, 'sd_minus_2' => 97.6, 'sd_minus_1' => 102.2, 'sd_plus_1' => 111.3, 'sd_plus_2' => 115.9, 'sd_plus_3' => 117.1],
        56 => ['median' => 107.3, 'sd_minus_3' => 93.4, 'sd_minus_2' => 98.1, 'sd_minus_1' => 102.7, 'sd_plus_1' => 111.9, 'sd_plus_2' => 116.5, 'sd_plus_3' => 117.1],
        57 => ['median' => 107.8, 'sd_minus_3' => 93.9, 'sd_minus_2' => 98.5, 'sd_minus_1' => 103.2, 'sd_plus_1' => 112.5, 'sd_plus_2' => 117.1, 'sd_plus_3' => 117.1],
        58 => ['median' => 108.4, 'sd_minus_3' => 94.3, 'sd_minus_2' => 99.0, 'sd_minus_1' => 103.7, 'sd_plus_1' => 113.0, 'sd_plus_2' => 117.7, 'sd_plus_3' => 117.1],
        59 => ['median' => 108.9, 'sd_minus_3' => 94.7, 'sd_minus_2' => 99.5, 'sd_minus_1' => 104.2, 'sd_plus_1' => 113.6, 'sd_plus_2' => 118.3, 'sd_plus_3' => 117.1],
        60 => ['median' => 109.4, 'sd_minus_3' => 95.2, 'sd_minus_2' => 99.9, 'sd_minus_1' => 104.7, 'sd_plus_1' => 114.2, 'sd_plus_2' => 118.9, 'sd_plus_3' => 117.1],
    ];

    $zScoreTableTBU = $jenis_kelamin === "L" ? $zScoreTableTBU_L : $zScoreTableTBU_P;
    if (isset($zScoreTableTBU[$usia_bulan])) {
        $medianTB = $zScoreTableTBU[$usia_bulan]['median'];
        $sd_minus_3 = $zScoreTableTBU[$usia_bulan]['sd_minus_3'];
        $sd_minus_2 = $zScoreTableTBU[$usia_bulan]['sd_minus_2'];
        $sd_minus_1 = $zScoreTableTBU[$usia_bulan]['sd_minus_1'];
        $sd_plus_1 = $zScoreTableTBU[$usia_bulan]['sd_plus_1'];
        $sd_plus_2 = $zScoreTableTBU[$usia_bulan]['sd_plus_2'];
        $sd_plus_3 = $zScoreTableTBU[$usia_bulan]['sd_plus_3'];

        // Tentukan status berdasarkan Z-score
        if ($tinggi_badan < $sd_minus_3) {
            return 'Sangat Pendek';
        } elseif ($tinggi_badan >= $sd_minus_3 && $tinggi_badan < $sd_minus_2) {
            return 'Pendek';
        } elseif ($tinggi_badan >= $sd_minus_2 && $tinggi_badan <= $sd_plus_1) {
            return 'Normal';
        } elseif ($tinggi_badan > $sd_plus_1 && $tinggi_badan <= $sd_plus_2) {
            return 'Tinggi';
        } else {
            return 'Sangat Tinggi';
        }
    }

    return 'Lulus';
}

    public function grafik(Request $request)
    {
        // Menyiapkan daftar bulan dalam Bahasa Indonesia
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Menyiapkan daftar tahun unik dari data pengukuran
        $tahunList = Pengukuran::selectRaw('YEAR(tanggal_pengukuran) as tahun')
                    ->distinct()
                    ->orderBy('tahun', 'asc')
                    ->pluck('tahun')
                    ->toArray();

        return view('pengukuran.grafik', 
            [
                'bulanList' => $bulanList,
                'tahunList' => $tahunList,
                'judul' => 'Grafik BB/U dan TB/U'
            ]
        );
    }

    public function dataPerBulanTahun($bulan, $tahun)
{
    // Mengonversi bulan dari Bahasa Indonesia ke format numerik
    $bulanNumerik = $this->bulanIndonesiaToNumerik($bulan);

    if (!$bulanNumerik) {
        return response()->json(['error' => 'Bulan tidak valid'], 400);
    }

    // Mengambil data pengukuran untuk bulan dan tahun yang dipilih
    $pengukuran = Pengukuran::with('profile')
        ->whereMonth('tanggal_pengukuran', '=', $bulanNumerik)
        ->whereYear('tanggal_pengukuran', '=', $tahun)
        ->get();

    // Siapkan array untuk menyimpan data
    $dataPerBulanTahun = [
        'bb_u' => [],
        'tb_u' => []
    ];

    foreach ($pengukuran as $data) {
        // Mengumpulkan data BB/U dan TB/U
        $dataPerBulanTahun['bb_u'][] = $data->status_bb_u;
        $dataPerBulanTahun['tb_u'][] = $data->status_tb_u;
    }

    // Menyiapkan data untuk grafik
    $bb_u_counts = array_count_values($dataPerBulanTahun['bb_u']);
    $tb_u_counts = array_count_values($dataPerBulanTahun['tb_u']);

    return response()->json([
            // BB/U Status
            'bb_u_normal' => isset($bb_u_counts['Normal']) ? $bb_u_counts['Normal'] : 0,
            'bb_u_sangat_kurang' => isset($bb_u_counts['Sangat Kurang']) ? $bb_u_counts['Sangat Kurang'] : 0,
            'bb_u_gizi_kurang' => isset($bb_u_counts['Gizi Kurang']) ? $bb_u_counts['Gizi Kurang'] : 0,
            'bb_u_gizi_lebih' => isset($bb_u_counts['Gizi Lebih']) ? $bb_u_counts['Gizi Lebih'] : 0,
            'bb_u_obesitas' => isset($bb_u_counts['Obesitas']) ? $bb_u_counts['Obesitas'] : 0,
            'bb_u_lulus' => isset($bb_u_counts['Lulus']) ? $bb_u_counts['Lulus'] : 0,

            // TB/U Status
            'tb_u_normal' => isset($tb_u_counts['Normal']) ? $tb_u_counts['Normal'] : 0,
            'tb_u_sangat_pendek' => isset($tb_u_counts['Sangat Pendek']) ? $tb_u_counts['Sangat Pendek'] : 0,
            'tb_u_pendek' => isset($tb_u_counts['Pendek']) ? $tb_u_counts['Pendek'] : 0,
            'tb_u_tinggi' => isset($tb_u_counts['Tinggi']) ? $tb_u_counts['Tinggi'] : 0,
            'tb_u_lulus' => isset($tb_u_counts['Lulus']) ? $tb_u_counts['Lulus'] : 0,
    ]);
}


    private function bulanIndonesiaToNumerik($bulanIndonesia)
    {
        $bulanMap = [
            'Januari' => '01',
            'Februari' => '02',
            'Maret' => '03',
            'April' => '04',
            'Mei' => '05',
            'Juni' => '06',
            'Juli' => '07',
            'Agustus' => '08',
            'September' => '09',
            'Oktober' => '10',
            'November' => '11',
            'Desember' => '12'
        ];

        return $bulanMap[$bulanIndonesia] ?? null;
    }

    public function destroy(Pengukuran $pengukuran)
    {
        if (!in_array(auth()->user()->role, ['Admin', 'Kader'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
        
        // Menghapus data pengukuran
        $pengukuran->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil dihapus');
    }

    public function kms($id)
    {
        // Mengambil data pengukuran untuk profile balita berdasarkan ID
        $profile = Profile::findOrFail($id);

        // Ambil data pengukuran balita yang terkait
        $pengukuran = Pengukuran::where('profile_id', $id)->get();

        // Menyiapkan data untuk grafik
        $bb_u = $pengukuran->pluck('status_bb_u');
        $tb_u = $pengukuran->pluck('status_tb_u');
        $tanggal_pengukuran = $pengukuran->pluck('tanggal_pengukuran');

        // Mengirim data ke view
        return view('pengukuran.kms', compact('profile', 'bb_u', 'tb_u', 'tanggal_pengukuran'));
    }

}

