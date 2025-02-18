<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('profile', function (Blueprint $table) {
        $table->decimal('bb_lahir', 5, 2)->change();
        $table->decimal('tb_lahir', 5, 2)->change();
    });
}

public function down(): void
{
    Schema::table('profile', function (Blueprint $table) {
        $table->decimal('bb_lahir')->change();
        $table->decimal('tb_lahir')->change();
    });
}

};
