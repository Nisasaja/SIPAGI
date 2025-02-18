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
        $pdfPath = asset('storage/pdf/buku-resep-kemenkes-2022.pdf');
        return view('informasi.recipe.index', compact('pdfPath'));
    }
}
