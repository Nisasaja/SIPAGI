<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Pengukuran;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandingPageController extends Controller
{
    public function landingPage()
    {
        $totalToddlers = Profile::count(); // Jumlah balita dari tabel `profiles`
        $totalUsers = User::count(); // Jumlah pengguna dari tabel `users`

        // Menghitung jumlah balita stunting berdasarkan data terakhir per balita
        $stuntingCount = Pengukuran::select('profile_id')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('pengukuran')
                    ->groupBy('profile_id');
            })
            ->whereIn('status_tb_u', ['Pendek', 'Sangat Pendek'])
            ->count();
        
        // Menghitung jumlah balita Gizi Kurang 
        $malNutrition = Pengukuran::select('profile_id')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('pengukuran')
                    ->groupBy('profile_id');
            })
            ->whereIn('status_bb_u', ['Gizi Kurang'])
            ->count();
        
        // Menghitung jumlah balita gizi buruk
        $malNutrition2 = Pengukuran::select('profile_id')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('pengukuran')
                    ->groupBy('profile_id');
            })
            ->whereIn('status_bb_u', ['Gizi Buruk'])
            ->count();


        // Menghitung jumlah balita normal berdasarkan data terakhir per balita
        $healthyCount = Pengukuran::select('profile_id')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('pengukuran')
                    ->groupBy('profile_id');
            })
            ->where('status_tb_u', 'Normal')
            ->count();
        
        $goodNutrition = Pengukuran::select('profile_id')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('pengukuran')
                    ->groupBy('profile_id');
            })
            ->where('status_bb_u', 'Normal')
            ->count();

        $data = [
            'totalToddlers' => $totalToddlers,
            'totalUsers' => $totalUsers,
            'stuntingCount' => $stuntingCount,  // Jumlah balita stunting
            'healthyCount' => $healthyCount,    // Jumlah balita sehat
            'malNutrition' => $malNutrition,    // Jumlah balita Gizi Kurang
            'malNutrition2' => $malNutrition2,   // Jumlah balita Gizi Buruk
            'goodNutrition' => $goodNutrition,  // Jumlah Balita Gizi Normal
        
        ];

        return view('welcome', compact('data'));
    }
}
