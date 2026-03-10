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
        Schema::create('riwayat_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id');
            $table->dateTime('absensi_masuk');
            $table->dateTime('absensi_keluar');
            $table->timestamps();


            $table->foreign('krs_id')
                ->references('id')
                ->on('krs')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_absensi');
    }
};
