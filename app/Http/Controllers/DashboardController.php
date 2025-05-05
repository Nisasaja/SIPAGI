<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Pengukuran;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Statistik Utama
        $jumlahBalita = Profile::count();
        $jumlahPengguna = User::count();

        // Menghitung balita lulus (status_tb_u 'Normal' berdasarkan data terakhir per balita)
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
        $bb_u_data = [];
        $tb_u_data = [];

        foreach ($dataPerTahun as $year => $data) {
            $bb_u_counts = array_count_values($data['bb_u']);
            $tb_u_counts = array_count_values($data['tb_u']);

            $bb_u_data[$year] = [
                'Normal' => $bb_u_counts['Normal'] ?? 0,
                'Gizi Buruk' => $bb_u_counts['Gizi Buruk'] ?? 0,
                'Gizi Kurang' => $bb_u_counts['Gizi Kurang'] ?? 0,
                'Gizi Lebih' => $bb_u_counts['Gizi Lebih'] ?? 0,
                'Obesitas' => $bb_u_counts['Obesitas'] ?? 0,
            ];

            $tb_u_data[$year] = [
                'Normal' => $tb_u_counts['Normal'] ?? 0,
                'Sangat Pendek' => $tb_u_counts['Sangat Pendek'] ?? 0,
                'Pendek' => $tb_u_counts['Pendek'] ?? 0,
                'Tinggi Badan Lebih' => $tb_u_counts['Tinggi Badan Lebih'] ?? 0,
            ];
        }

        // Mengelompokkan Data per Bulan dan Tahun untuk Chart Bulanan BB/U dan TB/U
        $pengukuranBulanan = Pengukuran::select(
            DB::raw("DATE_FORMAT(tanggal_pengukuran, '%Y-%m') as bulan"),
            'status_bb_u',
            'status_tb_u'
        )
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('pengukuran')
                ->groupBy(DB::raw("profile_id, DATE_FORMAT(tanggal_pengukuran, '%Y-%m')"));
        })
        ->get();

        // Struktur data untuk menyimpan jumlah masing-masing status per bulan
        $dataPerBulan = [];

        foreach ($pengukuranBulanan as $data) {
            $bulan = $data->bulan;
            if (!isset($dataPerBulan[$bulan])) {
                $dataPerBulan[$bulan] = [
                    'bb_u' => [],
                    'tb_u' => []
                ];
            }
            $dataPerBulan[$bulan]['bb_u'][] = $data->status_bb_u;
            $dataPerBulan[$bulan]['tb_u'][] = $data->status_tb_u;
        }

        // Persiapan Data untuk Chart BB/U dan TB/U Bulanan
        $bulan = array_keys($dataPerBulan);
        $bb_u_normal_bulanan = [];
        $bb_u_gizi_buruk_bulanan = [];
        $bb_u_gizi_kurang_bulanan = [];
        $bb_u_gizi_lebih_bulanan = [];
        $bb_u_obesitas_bulanan = [];
        $tb_u_normal_bulanan = [];
        $tb_u_stunting_bulanan = [];
        $tb_u_pendek_bulanan = [];
        $tb_u_tinggi_lebih_bulanan = [];

        foreach ($dataPerBulan as $data) {
            $bb_u_counts = array_count_values($data['bb_u']);
            $bb_u_normal_bulanan[] = $bb_u_counts['Normal'] ?? 0;
            $bb_u_gizi_buruk_bulanan[] = $bb_u_counts['Gizi Buruk'] ?? 0;
            $bb_u_gizi_kurang_bulanan[] = $bb_u_counts['Gizi Kurang'] ?? 0;
            $bb_u_gizi_lebih_bulanan[] = $bb_u_counts['Gizi Lebih'] ?? 0;
            $bb_u_obesitas_bulanan[] = $bb_u_counts['Obesitas'] ?? 0;

            $tb_u_counts = array_count_values($data['tb_u']);
            $tb_u_normal_bulanan[] = $tb_u_counts['Normal'] ?? 0;
            $tb_u_stunting_bulanan[] = $tb_u_counts['Sangat Pendek'] ?? 0;
            $tb_u_pendek_bulanan[] = $tb_u_counts['Pendek'] ?? 0;
            $tb_u_tinggi_lebih_bulanan[] = $tb_u_counts['Tinggi Badan Lebih'] ?? 0;
        }

        // Convert month format to month names
        $bulan = array_map(function($date) {
            return Carbon::createFromFormat('Y-m', $date)->format('F');
        }, $bulan);

        // Get all years for the dropdown
        $years = Pengukuran::select(DB::raw('YEAR(tanggal_pengukuran) as year'))
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('web.dashboard', compact(
            'jumlahBalita',
            'jumlahPengguna',
            'jumlahBalitaLulus',
            'balitaTeridentifikasiStunting',
            'tahun',
            'bb_u_data',
            'tb_u_data',
            'bulan',
            'bb_u_normal_bulanan',
            'bb_u_gizi_buruk_bulanan',
            'bb_u_gizi_kurang_bulanan',
            'bb_u_gizi_lebih_bulanan',
            'bb_u_obesitas_bulanan',
            'tb_u_normal_bulanan',
            'tb_u_stunting_bulanan',
            'tb_u_pendek_bulanan',
            'tb_u_tinggi_lebih_bulanan',
            'years'
        ));
    }

    public function getMonthlyData($year)
    {
        // Mengelompokkan Data per Bulan untuk Chart Bulanan BB/U dan TB/U
        $pengukuranBulanan = Pengukuran::select(
            DB::raw("DATE_FORMAT(tanggal_pengukuran, '%Y-%m') as bulan"),
            'status_bb_u',
            'status_tb_u'
        )
        ->whereYear('tanggal_pengukuran', $year)
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('pengukuran')
                ->groupBy(DB::raw("profile_id, DATE_FORMAT(tanggal_pengukuran, '%Y-%m')"));
        })
        ->get();

        // Struktur data untuk menyimpan jumlah masing-masing status per bulan
        $dataPerBulan = [];

        foreach ($pengukuranBulanan as $data) {
            $bulan = $data->bulan;
            if (!isset($dataPerBulan[$bulan])) {
                $dataPerBulan[$bulan] = [
                    'bb_u' => [],
                    'tb_u' => []
                ];
            }
            $dataPerBulan[$bulan]['bb_u'][] = $data->status_bb_u;
            $dataPerBulan[$bulan]['tb_u'][] = $data->status_tb_u;
        }

        // Persiapan Data untuk Chart BB/U dan TB/U Bulanan
        $bulan = array_keys($dataPerBulan);
        $bb_u_normal_bulanan = [];
        $bb_u_gizi_buruk_bulanan = [];
        $bb_u_gizi_kurang_bulanan = [];
        $bb_u_gizi_lebih_bulanan = [];
        $bb_u_obesitas_bulanan = [];
        $tb_u_normal_bulanan = [];
        $tb_u_stunting_bulanan = [];
        $tb_u_pendek_bulanan = [];
        $tb_u_tinggi_lebih_bulanan = [];

        foreach ($dataPerBulan as $data) {
            $bb_u_counts = array_count_values($data['bb_u']);
            $bb_u_normal_bulanan[] = $bb_u_counts['Normal'] ?? 0;
            $bb_u_gizi_buruk_bulanan[] = $bb_u_counts['Gizi Buruk'] ?? 0;
            $bb_u_gizi_kurang_bulanan[] = $bb_u_counts['Gizi Kurang'] ?? 0;
            $bb_u_gizi_lebih_bulanan[] = $bb_u_counts['Gizi Lebih'] ?? 0;
            $bb_u_obesitas_bulanan[] = $bb_u_counts['Obesitas'] ?? 0;

            $tb_u_counts = array_count_values($data['tb_u']);
            $tb_u_normal_bulanan[] = $tb_u_counts['Normal'] ?? 0;
            $tb_u_stunting_bulanan[] = $tb_u_counts['Sangat Pendek'] ?? 0;
            $tb_u_pendek_bulanan[] = $tb_u_counts['Pendek'] ?? 0;
            $tb_u_tinggi_lebih_bulanan[] = $tb_u_counts['Tinggi Badan Lebih'] ?? 0;
        }

        // Convert month format to month names
        $bulan = array_map(function($date) {
            return Carbon::createFromFormat('Y-m', $date)->format('F');
        }, $bulan);

        return response()->json([
            'bulan' => $bulan,
            'bb_u_normal_bulanan' => $bb_u_normal_bulanan,
            'bb_u_gizi_buruk_bulanan' => $bb_u_gizi_buruk_bulanan,
            'bb_u_gizi_kurang_bulanan' => $bb_u_gizi_kurang_bulanan,
            'bb_u_gizi_lebih_bulanan' => $bb_u_gizi_lebih_bulanan,
            'bb_u_obesitas_bulanan' => $bb_u_obesitas_bulanan,
            'tb_u_normal_bulanan' => $tb_u_normal_bulanan,
            'tb_u_stunting_bulanan' => $tb_u_stunting_bulanan,
            'tb_u_pendek_bulanan' => $tb_u_pendek_bulanan,
            'tb_u_tinggi_lebih_bulanan' => $tb_u_tinggi_lebih_bulanan
        ]);
    }
}
