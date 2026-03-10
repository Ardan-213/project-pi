<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

Auth::routes();

Route::prefix('internal')
    // ->middleware('auth')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // permissions
        Route::get('permission', [PermissionController::class, 'index'])
            ->name('permission');

        Route::get('role', [RoleController::class, 'index'])
            ->name('role');
        Route::get('role/tambah', [RoleController::class, 'tambah'])
            ->name('role.tambah');
        Route::post('role/simpan', [RoleController::class, 'simpan'])
            ->name('role.simpan');
        Route::get('role/hapus/{id}', [RoleController::class, 'hapus'])
            ->name('role.hapus');

        // users
        Route::get('user', [UserController::class, 'index'])
            ->name('user');

        // fakultas
        Route::get('fakultas', [FakultasController::class, 'index'])
            ->name('fakultas');
        Route::get('fakultas/tambah', [FakultasController::class, 'tambah'])
            ->name('fakultas.tambah');
        Route::get('fakultas/data', [FakultasController::class, 'data'])
            ->name('fakultas.data');
        Route::post('fakultas/simpan', [FakultasController::class, 'simpan'])
            ->name('fakultas.simpan');
        Route::post('fakultas/hapus', [FakultasController::class, 'hapus'])
            ->name('fakultas.hapus');

        Route::get('jurusan', [JurusanController::class, 'index'])->name('jurusan');
        Route::get('jurusan/data', [JurusanController::class, 'data'])->name('jurusan.data');
        Route::get('jurusan/tambah', [JurusanController::class, 'tambah'])->name('jurusan.tambah');
        Route::get('jurusan/listFakultas', [JurusanController::class, 'listFakultas'])->name('jurusan.listFakultas');
        Route::get('jurusan/listFakultasByJurusan', [JurusanController::class, 'listFakultasByJurusan'])->name('jurusan.listFakultasByJurusan');
        Route::post('jurusan/simpan', [JurusanController::class, 'simpan'])->name('jurusan.simpan');
        Route::post('jurusan/hapus', [JurusanController::class, 'hapus'])->name('jurusan.hapus');
        Route::get('jurusan/edit/{id}', [JurusanController::class, 'edit'])->name('jurusan.edit');
        Route::post('jurusan/update', [JurusanController::class, 'update'])->name('jurusan.update');


        // dosen
        Route::get('dosen', [DosenController::class, 'index'])->name('dosen');
        Route::get('dosen/tambah', [DosenController::class, 'tambah'])->name('dosen.tambah');
        Route::post('dosen/simpan', [DosenController::class, 'simpan'])->name('dosen.simpan');
        Route::get('dosen/listJurusan', [DosenController::class, 'listJurusan'])->name('dosen.listJurusan');
        Route::get('dosen/data', [DosenController::class, 'data'])->name('dosen.data');
        Route::post('dosen/simpan', [DosenController::class, 'simpan'])->name('dosen.simpan');
        Route::get('dosen/edit/{id}', [DosenController::class, 'edit'])->name('dosen.edit');
        Route::post('dosen/update', [DosenController::class, 'update'])->name('dosen.update');
        Route::post('dosen/hapus', [DosenController::class, 'hapus'])->name('dosen.hapus');


        // mata kuliah
        Route::get('mata-kuliah', [MataKuliahController::class, 'index'])
            ->name('mata-kuliah');
        Route::get('mata-kuliah/data', [MataKuliahController::class, 'data'])
            ->name('mata-kuliah.data');
        Route::get('mata-kuliah/tambah', [MataKuliahController::class, 'tambah'])
            ->name('mata-kuliah.tambah');
        Route::get('mata-kuliah/listJurusan', [MataKuliahController::class, 'listJurusan'])
            ->name('mata-kuliah.listJurusan');
        Route::get('mata-kuliah/listDosenByJurusan', [MataKuliahController::class, 'listDosenByJurusan'])
            ->name('mata-kuliah.listDosenByJurusan');
        Route::post('mata-kuliah/simpan', [MataKuliahController::class, 'simpan'])
            ->name('mata-kuliah.simpan');
        Route::get('mata-kuliah/edit/{id}', [MataKuliahController::class, 'edit'])
            ->name('mata-kuliah.edit');
        Route::post('mata-kuliah/update', [MataKuliahController::class, 'update'])
            ->name('mata-kuliah.update');
        Route::post('mata-kuliah/hapus', [MataKuliahController::class, 'hapus'])
            ->name('mata-kuliah.hapus');


        // mahasiswa
        Route::get('mahasiswa', [MahasiswaController::class, 'index'])
            ->name('mahasiswa');
        Route::get('mahasiswa/data', [MahasiswaController::class, 'data'])
            ->name('mahasiswa.data');
        Route::get('mahasiswa/tambah', [MahasiswaController::class, 'tambah'])
            ->name('mahasiswa.tambah');
        Route::post('mahasiswa/simpan', [MahasiswaController::class, 'simpan'])
            ->name('mahasiswa.simpan');
        Route::get('mahasiswa/listJurusan', [MahasiswaController::class, 'listJurusan'])
            ->name('mahasiswa.listJurusan');


        // krs
        Route::get('krs', [KrsController::class, 'index'])
            ->name('krs');
        Route::post('krs/input-krs', [KrsController::class, 'inputKrs'])
            ->name('krs.inputKrs');
        Route::get('krs/data', [KrsController::class, 'data'])
            ->name('krs.data');
        Route::post('krs/hapus', [KrsController::class, 'hapus'])
            ->name('krs.hapus');


        // bagian face recogination
        Route::get('daftar-wajah', [FaceController::class, 'daftar_wajah'])
            ->name('daftar-wajah');
        Route::post('simpanDaftarWajah', [FaceController::class, 'simpanDaftarWajah'])
            ->name('simpanDaftarWajah');
        Route::get('halaman-absensi/{id}', [FaceController::class, 'halaman_deteksi_absensi'])
            ->name('halaman_deteksi_absensi');
        Route::get('descriptors', [FaceController::class, 'descriptors']);
        Route::post('absensi', [FaceController::class, 'absensi']);
        Route::get('dataRiwayatAbsensi', [KrsController::class, 'dataRiwayatAbsensi'])
            ->name('dataRiwayatAbsensi');
    });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
