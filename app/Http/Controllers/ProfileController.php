<?php

namespace App\Http\Controllers;
use App\Models\Profile;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log; 


class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Profile::query();
        $perPage = $request->query('perPage', 10); // Menggunakan nilai 'perPage' dari query, default 10
        
        // Filter berdasarkan desa
        if ($request->has('desa') && $request->desa) {
            $query->where('alamat', $request->desa);
        }

        // Pencarian berdasarkan input
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_anak', 'LIKE', "%{$search}%")
                    ->orWhere('jenis_kelamin', 'LIKE', "%{$search}%")
                    ->orWhere('anak_ke', 'LIKE', "%{$search}%")
                    ->orWhere('alamat', 'LIKE', "%{$search}%")
                    ->orWhere('status_asi', 'LIKE', "%{$search}%")
                    ->orWhere('kepemilikan_jamban', 'LIKE', "%{$search}%")
                    ->orWhere('riwayat_kesehatan', 'LIKE', "%{$search}%")
                    ->orWhere('bb_lahir', 'LIKE', "%{$search}%")
                    ->orWhere('tb_lahir', 'LIKE', "%{$search}%")
                    ->orWhere('status_imunisasi', 'LIKE', "%{$search}%");
            });
        }

        // Paginate data sesuai dengan 'perPage' dan append parameter search dan desa
        $profiles = $query->paginate($perPage)->appends($request->only('search', 'desa', 'perPage'));

        // Dapatkan daftar desa untuk dropdown
        $listDesa = Profile::select('alamat')->distinct()->pluck('alamat');

        return view('profiles.index', compact('profiles', 'listDesa'));
    }

    public function downloadProfilePdf(Request $request)
    {
        // Logging (opsional)
        Log::info('Download Profile PDF triggered', [
            'search' => $request->input('search'),
        ]);

        $search = $request->input('search');

        $query = Profile::query();

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where('nama_anak', 'like', '%' . $search . '%');
        }

        // Ambil data profile
        $profiles = $query->get();

        // Kirim data ke view untuk PDF
        $pdf = Pdf::loadView('profiles.pdf', compact('profiles'));

        // Nama file PDF
        $filename = 'data_profile_' . now()->format('Ymd_His') . '.pdf';

        // Mengembalikan file PDF untuk diunduh
        return $pdf->download($filename);
    }

    // Form untuk membuat data baru
    public function create()
    {
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Kader') {
            abort(403, 'Unauthorized action.');
        }

        return view('profiles.create', [
            'judul' => 'Tambah Data'
        ]);
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $this->validateData($request);

        // Simpan data ke database
        Profile::create($validatedData);

        return redirect()->route('profiles.index')->with('success', 'Profil berhasil disimpan.');
    }

    // Menampilkan detail profil
    public function show(Profile $profile)
    {
        return view('profiles.show', [
            'judul' => 'Lihat Data',
            'profile' => $profile
        ]);
    }

    // Menampilkan form edit
    public function edit(Profile $profile)
    {
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Kader') {
            abort(403, 'Unauthorized action.');
        }

        return view('profiles.edit', [
            'judul' => 'Edit Data',
            'profile' => $profile
        ]);
    }

    // Memperbarui data profil
    public function update(Request $request, Profile $profile)
    {
        // Validasi data
        $validatedData = $this->validateData($request);

        // Update data di database
        $profile->update($validatedData);

        return redirect()->route('profiles.index')->with('success', 'Profil berhasil diperbarui.');
    }

    // Menghapus data profil
    public function destroy(Profile $profile)
    {
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Kader') {
            abort(403, 'Unauthorized action.');
        }

        $profile->delete();

        return redirect()->route('profiles.index')->with('success', 'Profil berhasil dihapus.');
    }
    
    // Fungsi validasi
    private function validateData(Request $request)
    {
        return $request->validate([
            // Data Orang Tua
            'nama_ibu' => 'required|string|max:255',
            'usia_ibu' => 'required|integer',
            'pendidikan_ibu' => 'required|string|max:255',
            'pekerjaan_ibu' => 'required|string|max:255',
            'nama_ayah' => 'required|string|max:255',
            'pendidikan_ayah' => 'required|string|max:255',
            'pekerjaan_ayah' => 'required|string|max:255',
            
            // Data Anak
            'nama_anak' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|in:Laki-Laki,Perempuan',
            'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:255',
            'anak_ke' => 'required|integer',
            'status_asi' => 'required|string|in:Ekslusif,Tidak Ekslusif',
            'status_imunisasi' => 'required|string|in:Lengkap,Tidak Lengkap',
            'bb_lahir' => 'required|numeric|min:0',
            'tb_lahir' => 'required|numeric|min:0',
            
            // Data Sanitasi
            'kepemilikan_jamban' => 'required|string|in:Ada,Tidak Ada',
            'luas_rumah' => 'required|string|max:255',
            'lantai_rumah' => 'required|string|max:255',
            'jml_penghuni' => 'required|integer|min:0',
            'alat_masak' => 'required|string|max:255',
            'sumber_air' => 'required|string|max:255',
            'riwayat_kesehatan' => 'required|string|max:255',
        ]);
    }
}
