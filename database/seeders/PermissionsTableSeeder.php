<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // //permission for roles
        // Permission::create(['name' => 'akses halaman role']);
        // Permission::create(['name' => 'akses tambah role']);
        // Permission::create(['name' => 'akses edit role']);
        // Permission::create(['name' => 'akses hapus role']);

        // // //permission for permissions
        // Permission::create(['name' => 'akses halaman permission']);

        // // //permission for users
        // Permission::create(['name' => 'akses halaman user']);
        // Permission::create(['name' => 'akses tambah user']);
        // Permission::create(['name' => 'akses edit user']);
        // Permission::create(['name' => 'akses hapus user']);


        // // // fakultas
        // Permission::create(['name' => 'akses halaman fakultas']);
        // Permission::create(['name' => 'akses tambah fakultas']);
        // Permission::create(['name' => 'akses edit fakultas']);
        // Permission::create(['name' => 'akses hapus fakultas']);

        // // // jurusan
        // Permission::create(['name' => 'akses halaman jurusan']);
        // Permission::create(['name' => 'akses tambah jurusan']);
        // Permission::create(['name' => 'akses edit jurusan']);
        // Permission::create(['name' => 'akses hapus jurusan']);

        // // dosen
        // Permission::create(['name' => 'akses halaman dosen']);
        // Permission::create(['name' => 'akses tambah dosen']);
        // Permission::create(['name' => 'akses edit dosen']);
        // Permission::create(['name' => 'akses hapus dosen']);

        // // mata kuliah
        // Permission::create(['name' => 'akses halaman mata kuliah']);
        // Permission::create(['name' => 'akses tambah mata kuliah']);
        // Permission::create(['name' => 'akses edit mata kuliah']);
        // Permission::create(['name' => 'akses hapus mata kuliah']);

        // // mahasiswa

        // Permission::create(['name' => 'akses halaman mahasiswa']);
        // Permission::create(['name' => 'akses tambah mahasiswa']);
        // Permission::create(['name' => 'akses edit mahasiswa']);
        // Permission::create(['name' => 'akses hapus mahasiswa']);


        // akses mahasiswa tambahan
        Permission::create(['name' => 'akses halaman pengaturan wajah']);
        Permission::create(['name' => 'akses halaman krs']);
        Permission::create(['name' => 'akses halaman absensi']);
    }
}
