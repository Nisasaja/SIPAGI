<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('pengukuran', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('profile_id');
        $table->date('tanggal_pengukuran');
        $table->double('berat_badan', 5, 2);
        $table->double('tinggi_badan', 5, 2);
        $table->string('status_bb_u');
        $table->string('status_tb_u');
        $table->timestamps();

        // Foreign key relationship to "profils" table
        $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
    });
}


public function down()
{
    Schema::dropIfExists('pengukuran');
}

};
