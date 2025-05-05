<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\GaleriKegiatan;

class GaleriKegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $galeri = GaleriKegiatan::paginate(10);
        return view('informasi.galeri.index', compact('galeri'));
    }

    public function create()
    {
        return view('informasi.galeri.create');
    }

    public function edit($id)
    {
        $galeri = GaleriKegiatan::findOrFail($id);
        return view('informasi.galeri.edit', compact('galeri'));
    }

    public function show($id)
    {
        $galeri = GaleriKegiatan::findOrFail($id);
        if (in_array(auth()->user()->role, ['Admin', 'Kader', 'Manager'])) {
            return view('informasi.galeri.show', compact('galeri'));
        } else {
            return redirect()->route('landingpage')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $path = $request->file('gambar')->store('galeri', 'public');
        $validated['gambar'] = $path;

        GaleriKegiatan::create($validated);
        return redirect()->route('informasi.galeri.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    public function destroy(GaleriKegiatan $galeri)
    {
        Storage::delete('public/' . $galeri->gambar);
        $galeri->delete();
        return back()->with('success', 'Galeri berhasil dihapus.');
    }
}
