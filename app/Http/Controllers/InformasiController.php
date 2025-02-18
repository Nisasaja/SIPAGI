<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class InformasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function videoIndex()
    {
        $videos = Video::all();
        return view('informasi.video.index', compact('videos'));
    }

    // Form untuk menambah video baru
    public function videoCreate()
    {
        return view('informasi.video.create');
    }

    // Simpan video baru
    public function videoStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        // Mengubah URL menjadi format embed jika diperlukan
        $url = $request->input('url');
        
        // Cek apakah URL berupa link YouTube dan ubah ke format embed
        if (strpos($url, 'youtu.be') !== false) {
            $videoId = basename($url); // Ambil video ID dari URL pendek
            $url = "https://www.youtube.com/embed/{$videoId}";
        } elseif (strpos($url, 'watch?v=') !== false) {
            // Ubah URL standar YouTube menjadi embed
            $url = str_replace('watch?v=', 'embed/', $url);
        }

        // Simpan video dengan URL yang sudah diubah
        Video::create([
            'title' => $request->input('title'),
            'url' => $url,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('informasi.video.index')->with('success', 'Video berhasil ditambahkan.');
    }

    // Form edit video
    public function videoEdit(Video $video)
    {
        return view('informasi.video.edit', compact('video'));
    }

    // Update data video
    public function videoUpdate(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        // Mengubah URL menjadi format embed jika diperlukan
        $url = $request->input('url');
        
        // Cek apakah URL berupa link YouTube dan ubah ke format embed
        if (strpos($url, 'youtu.be') !== false) {
            $videoId = basename($url); // Ambil video ID dari URL pendek
            $url = "https://www.youtube.com/embed/{$videoId}";
        } elseif (strpos($url, 'watch?v=') !== false) {
            // Ubah URL standar YouTube menjadi embed
            $url = str_replace('watch?v=', 'embed/', $url);
        }

        // Update video dengan URL yang sudah diubah
        $video->update([
            'title' => $request->input('title'),
            'url' => $url,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('informasi.video.index')->with('success', 'Video berhasil diupdate.');
    }

    // Hapus video
    public function videoDestroy(Video $video)
    {
        $video->delete();

        return redirect()->route('informasi.video.index')->with('success', 'Video berhasil dihapus.');
    }
}
