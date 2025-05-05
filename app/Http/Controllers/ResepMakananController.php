<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResepMakananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function recipeIndex()
    {
        $pdfFiles = [
            ['title' => 'Buku Resep Kemenkes 2023', 'path' => asset('storage/pdf/buku-resep-kemenkes-2022.pdf')],
            ['title' => 'Buku Resep PMT Lokal Persagi Banyumas', 'path' => asset('storage/pdf/menu_PMT_Lokal_Persagi_Banyumas.pdf')],
            ['title' => 'Buku Asuhan Gizi Untuk Bayi, Balita, dan Anak', 'path' => asset('storage/pdf/asuhan_gizi_untuk_balita_stunting.pdf')],
        ];

        return view('informasi.recipe.index', compact('pdfFiles'));
    }
}
