<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Petugas;
use Illuminate\Support\Facades\Storage;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::all();
        return view('petugas.index', compact('petugas'));
    }

    /**
     * Menampilkan form untuk menambah petugas baru.
     */
    public function create()
    {
        return view('petugas.create');
    }

    /**
     * Menyimpan petugas baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'address' => 'required|string',
            'jabatan' => 'required|string',
            'tempat_bertugas' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'status' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tahun_bergabung' => 'required|string',
            'bpjs' => 'required|string',
            'nomor_bpjs' => 'nullable|string',
            'email' => 'required|email|unique:petugas,email',
            'phone' => 'required|string|max:15',
            'nik' => 'required|string|unique:petugas,nik',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Menyimpan foto jika ada
        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('petugas_photos', 'public');
        }

        // Menyimpan data petugas ke database
        Petugas::create($validatedData);

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail petugas tertentu.
     */
    public function show(Petugas $petugas)
    {
        return view('petugas.show', compact('petugas'));
    }

    /**
     * Menampilkan form untuk mengedit petugas tertentu.
     */
    public function edit(Petugas $petugas)
    {
        return view('petugas.edit', compact('petugas'));
    }

    /**
     * Memperbarui data petugas tertentu.
     */
    public function update(Request $request, Petugas $petugas)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'address' => 'required|string',
            'jabatan' => 'required|string',
            'tempat_bertugas' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'status' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tahun_bergabung' => 'required|string',
            'bpjs' => 'required|string',
            'nomor_bpjs' => 'nullable|string',
            'email' => 'required|email|unique:petugas,email,' . $petugas->id,
            'phone' => 'required|string|max:15',
            'nik' => 'required|string|unique:petugas,nik,' . $petugas->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Menyimpan foto baru jika ada
        if ($request->hasFile('photo')) {
            // Menghapus foto lama jika ada
            if ($petugas->photo) {
                Storage::delete('public/' . $petugas->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('petugas_photos', 'public');
        }

        // Memperbarui data petugas
        $petugas->update($validatedData);

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil diperbarui.');
    }

    /**
     * Menghapus petugas tertentu.
     */
    public function destroy(Petugas $petugas)
    {
        // Menghapus foto jika ada
        if ($petugas->photo) {
            Storage::delete('public/' . $petugas->photo);
        }

        // Menghapus data petugas
        $petugas->delete();

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}
