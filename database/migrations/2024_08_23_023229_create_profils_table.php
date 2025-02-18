<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            // data orang tua
            $table->string('nama_ibu');
            $table->integer('usia_ibu');
            $table->string('pendidikan_ibu');
            $table->string('pekerjaan_ibu');
            $table->string('nama_ayah');
            $table->string('pendidikan_ayah');
            $table->string('pekerjaan_ayah');
            // data anak
            $table->string('nama_anak');
            $table->string('jenis_kelamin');
            $table->date('tanggal_lahir');
            $table->string('alamat');
            $table->integer('anak_ke');
            $table->string('status_asi');
            $table->string('status_imunisasi');
            $table->decimal('bb_lahir', 5, 2);
            $table->decimal('tb_lahir', 5, 2);
            // data sanitasi
            $table->string('kepemilikan_jamban');
            $table->string('luas_rumah');
            $table->string('lantai_rumah');
            $table->integer('jml_penghuni');
            $table->string('alat_masak');
            $table->string('sumber_air');
            $table->string('riwayat_kesehatan');
            $table->timestamps();
        });    
        
    }

    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
