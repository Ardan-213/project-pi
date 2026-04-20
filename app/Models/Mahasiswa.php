<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'nama',
        'npm',
        'jurusan',
        'jenis_kelamin',
        'face_descriptor',
        'users_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class, 'mahasiswa_id', 'id');
    }
}
