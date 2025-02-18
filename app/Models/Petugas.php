<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;
    protected $table = 'petugas';

    protected $fillable = [
        'name', 
        'birthdate', 
        'address', 
        'jabatan',
        'tempat_bertugas',
        'pendidikan_terakhir',
        'status',
        'jenis_kelamin',
        'tahun_bergabung',
        'bpjs', 
        'nomor_bpjs',
        'email',
        'phone', 
        'nik', 
        'photo'
    ];
}
