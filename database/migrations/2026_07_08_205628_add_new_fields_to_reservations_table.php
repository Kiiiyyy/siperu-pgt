<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // 1. Tambah field judul pengajuan setelah kolom id
            $table->string('judul_pengajuan')->after('id')->nullable();

            // 2. Tambah foreign key untuk Dosen Terkait yang dipilih mahasiswa
            $table->foreignId('lecturer_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->onDelete('set null');

            // 3. Tambah foreign key untuk Approval Admin yang dimintai persetujuan
            $table->foreignId('approval_admin_id')
                  ->nullable()
                  ->after('lecturer_id')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop foreign key dan kolomnya jika di-rollback
            $table->dropForeign(['lecturer_id']);
            $table->dropForeign(['approval_admin_id']);

            $table->dropColumn(['judul_pengajuan', 'lecturer_id', 'approval_admin_id']);
        });
    }
};
