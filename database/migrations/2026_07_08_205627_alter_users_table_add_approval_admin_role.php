<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah enum role lama dengan menambahkan 'approval_admin'
            // Laravel 12 mendukung native change untuk MySQL tanpa perlu library dbal tambahan
            $table->enum('role', ['admin', 'approval_admin', 'dosen', 'mahasiswa'])
                  ->default('mahasiswa')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke setelan awal jika di-rollback
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])
                  ->default('mahasiswa')
                  ->change();
        });
    }
};
