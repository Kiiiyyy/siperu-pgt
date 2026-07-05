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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nim')->unique(); // Menggantikan email untuk login
        $table->string('nama');
        $table->string('jurusan');
        $table->string('kelas');
        $table->string('password');
        $table->enum('role', ['admin', 'dosen', 'mahasiswa']); // Sesuai Class Diagram
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
