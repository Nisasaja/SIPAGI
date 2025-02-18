<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Pengukuran;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->role === 'Admin') {
    //         return view('admin.dashboard');
    //     } elseif ($user->role === 'Kader') {
    //         return view('kader.dashboard');
    //     } elseif ($user->role === 'Manager') {
    //         return view('manager.dashboard');
    //     }

    //     return abort(403, 'Anda tidak memiliki akses.');
    // }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
{
    // Statistik Utama
    $jumlahBalita = Profile::count();
    $jumlahPengguna = User::count();

    // Menghitung balita lulus (status_bb_u 'Normal' berdasarkan data terakhir per balita)
    $jumlahBalitaLulus = Pengukuran::select('profile_id')
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('pengukuran')
                ->groupBy('profile_id');
        })
        ->where('status_tb_u', 'Normal')
        ->count();

    // Menghitung balita teridentifikasi stunting (status_tb_u 'Pendek' atau 'Sangat Pendek' berdasarkan data terakhir per balita)
    $balitaTeridentifikasiStunting = Pengukuran::select('profile_id')
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('pengukuran')
                ->groupBy('profile_id');
        })
        ->whereIn('status_tb_u', ['Pendek', 'Sangat Pendek'])
        ->count();

    // Total Pengukuran
    $totalPengukuran = Pengukuran::count();

    // Statistik Status BB/U
    $giziBuruk = Pengukuran::where('status_bb_u', 'Gizi Buruk')->count();
    $giziKurang = Pengukuran::where('status_bb_u', 'Gizi Kurang')->count();
    $obesitas = Pengukuran::where('status_bb_u', 'Obesitas')->count();

    // Mengelompokkan Data per Tahun untuk Chart BB/U dan TB/U
    $pengukuranTerakhir = Pengukuran::whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
            ->from('pengukuran')
            ->groupBy('profile_id');
    })->get();

    $dataPerTahun = [];

    foreach ($pengukuranTerakhir as $data) {
        $tahun = Carbon::parse($data->tanggal_pengukuran)->format('Y');
        if (!isset($dataPerTahun[$tahun])) {
            $dataPerTahun[$tahun] = [
                'bb_u' => [],
                'tb_u' => []
            ];
        }
        $dataPerTahun[$tahun]['bb_u'][] = $data->status_bb_u;
        $dataPerTahun[$tahun]['tb_u'][] = $data->status_tb_u;
    }

    // Persiapan Data untuk Chart BB/U dan TB/U
    $tahun = array_keys($dataPerTahun);
    $bb_u_normal = [];
    $bb_u_gizi_buruk = [];
    $bb_u_gizi_kurang = [];
    $bb_u_gizi_lebih = [];
    $bb_u_obesitas = [];
    $tb_u_normal = [];
    $tb_u_stunting = [];
    $tb_u_pendek = [];
    $tb_u_tinggi_lebih = [];

    foreach ($dataPerTahun as $data) {
        $bb_u_counts = array_count_values($data['bb_u']);
        $bb_u_normal[] = $bb_u_counts['Normal'] ?? 0;
        $bb_u_gizi_buruk[] = $bb_u_counts['Gizi Buruk'] ?? 0;
        $bb_u_gizi_kurang[] = $bb_u_counts['Gizi Kurang'] ?? 0;
        $bb_u_gizi_lebih[] = $bb_u_counts['Gizi Lebih'] ?? 0;
        $bb_u_obesitas[] = $bb_u_counts['Obesitas'] ?? 0;

        $tb_u_counts = array_count_values($data['tb_u']);
        $tb_u_normal[] = $tb_u_counts['Normal'] ?? 0;
        $tb_u_stunting[] = $tb_u_counts['Sangat Pendek'] ?? 0;
        $tb_u_pendek[] = $tb_u_counts['Pendek'] ?? 0;
        $tb_u_tinggi_lebih[] = $tb_u_counts['Tinggi Badan Lebih'] ?? 0;
    }

    return view('web.dashboard', compact(
        'jumlahBalita',
        'jumlahPengguna',
        'jumlahBalitaLulus',
        'balitaTeridentifikasiStunting',
        'totalPengukuran',
        'giziBuruk',
        'obesitas',
        'tahun',
        'bb_u_normal',
        'bb_u_gizi_buruk',
        'bb_u_gizi_kurang',
        'bb_u_gizi_lebih',
        'bb_u_obesitas',
        'tb_u_normal',
        'tb_u_stunting',
        'tb_u_pendek',
        'tb_u_tinggi_lebih'
    ));
}
}
