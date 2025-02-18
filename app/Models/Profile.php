<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profile'; // Nama tabel di database

    protected $fillable = [
        'nama_ibu',
        'usia_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'nama_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'nama_anak',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'anak_ke',
        'status_asi',
        'status_imunisasi',
        'bb_lahir',
        'tb_lahir',
        'kepemilikan_jamban',
        'luas_rumah',
        'lantai_rumah',
        'jml_penghuni',
        'alat_masak',
        'sumber_air',
        'riwayat_kesehatan',
    ];
    public function getUsiaAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function pengukurans()
    {
        return $this->hasMany(Pengukuran::class);
    }

    // public function pengukurans()
    // {
    //     return $this->hasMany(Pengukuran::class, 'id_profile');
    // }
}
