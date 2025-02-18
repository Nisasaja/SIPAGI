<?php

namespace App\Http\Controllers;
use App\Models\Pengukuran;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KMSController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Ambil nilai pencarian dari input
        $search = $request->input('search');

        // Filter data berdasarkan nama_anak jika ada pencarian
        $profiles = Profile::when($search, function ($query, $search) {
            $query->where('nama_anak', 'like', '%' . $search . '%');
        })->get();

        // Tampilkan ke view dengan data yang telah difilter
        return view('grafik.index', compact('profiles'));
    }

    public function showKMS($id, $month = null)
    {
        // Ambil data profile berdasarkan ID
        $profile = Profile::findOrFail($id);

        // Ambil data pengukuran berdasarkan profile_id dan bulan yang dipilih
        $query = Pengukuran::where('profile_id', $id);

        if ($month) {
            // Filter berdasarkan bulan yang dipilih
            $query->whereMonth('tanggal_pengukuran', '=', $month);
        }

        $pengukuran = $query->get();

        $bbData = [];
        $tbData = [];
        $dates = [];

        foreach ($pengukuran as $data) {
            $dates[] = Carbon::parse($data->tanggal_pengukuran)->format('d M Y');
            $bbData[] = $this->calculateBBU($data->berat_badan, $profile->tanggal_lahir, $data->tanggal_pengukuran, $profile->jenis_kelamin);
            $tbData[] = $this->calculateTBU($data->tinggi_badan, $profile->tanggal_lahir, $data->tanggal_pengukuran, $profile->jenis_kelamin);
        }

        return view('grafik.kms', compact('profile', 'bbData', 'tbData', 'dates', 'month'));
    }

    private function calculateBBU($beratBadan, $tanggalLahir, $tanggalPengukuran, $jenisKelamin)
    {
        // Implementasi logika untuk menghitung BB/U berdasarkan umur dan jenis kelamin
        return $beratBadan; // Contoh sementara
    }

    private function calculateTBU($tinggiBadan, $tanggalLahir, $tanggalPengukuran, $jenisKelamin)
    {
        // Implementasi logika untuk menghitung TB/U berdasarkan umur dan jenis kelamin
        return $tinggiBadan; // Contoh sementara
    }
}
