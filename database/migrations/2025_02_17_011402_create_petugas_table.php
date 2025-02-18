<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birthdate');
            $table->text('address');
            $table->text('jabatan'); //misalnya kader,perawat,bidan,dll
            $table->text('tempat_bertugas'); //misalnya puskesmas, posyandu, dll
            $table->text('pendidikan_terakhir');
            $table->text('status');
            $table->text('jenis_kelamin');
            $table->string('tahun_bergabung');
            $table->string('bpjs'); //misalnya bpjs pemerintah, bpjs ketenagakerjaan dan tidak memiliki bpjs
            $table->string('nomor_bpjs')->nullable(); 
            $table->string('email')->unique();
            $table->string('phone', 15);
            $table->string('nik', 16)->unique();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
