<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengukuran extends Model
{
    use HasFactory;

    protected $table = 'pengukuran';

    protected $fillable = [
        'profile_id',
        'tanggal_pengukuran',
        'berat_badan',
        'tinggi_badan',
        'status_bb_u',
        'status_tb_u'
    ];

    // Relasi ke Profile
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
