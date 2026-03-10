<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode',' nama_mata_kuliah', 'jurusan_id',
        'dosen_id', 'sks', 'ruangan', 'hari',
        'waktu_mulai', 'waktu_selesai', 'kuota_orang'
    ];

    public function jurusan(){
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
    }

    public function dosen(){
        return $this->belongsTo(Dosen::class, 'dosen_id', 'id');
    }
}
