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
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama_mata_kuliah');
            $table->foreignId('jurusan_id');
            $table->foreignId('dosen_id');
            $table->integer('sks');
            $table->string('ruangan');
            $table->string('hari');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->timestamps();

            $table->foreign('jurusan_id')
            ->references('id')
            ->on('jurusan')
            ->onUpdate('cascade')
            ->onDelete('cascade');

             $table->foreign('dosen_id')
            ->references('id')
            ->on('dosen')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
