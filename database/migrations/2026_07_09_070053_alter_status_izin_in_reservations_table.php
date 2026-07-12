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
        Schema::table('reservations', function (Blueprint $table) {
            // Kita ubah dari ENUM kaku menjadi STRING biasa biar fleksibel pak
            $table->string('status_izin')->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Kembalikan ke enum semula jika di-rollback
            $table->enum('status_izin', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending')->change();
        });
    }
};
