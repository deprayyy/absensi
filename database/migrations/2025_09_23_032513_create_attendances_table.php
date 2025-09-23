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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relasi ke office (nullable supaya kalau kantor dihapus, attendance tidak ikut hilang)
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();

            // Tanggal absen
            $table->date('date');

            // Jam absen masuk & pulang
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
