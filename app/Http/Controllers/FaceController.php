<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaceController extends Controller
{
    public function halaman_absen_masuk($id)
    {
        $krs = DB::table('krs')
            ->select('krs.id as krs_id', 'krs.*', 'mahasiswa.nama as nama_mahasiswa', 'mata_kuliah.*', 'jurusan.nama as nama_jurusan', 'dosen.nama_lengkap as dosen')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->join('jurusan', 'jurusan.id', '=', 'mata_kuliah.jurusan_id')
            ->join('dosen', 'dosen.id', '=', 'mata_kuliah.dosen_id')
            ->where('krs.id', $id)->first();

        return view('pages.face-recogination.absen-masuk', [
            'krs' => $krs,
        ]);
    }

    public function halaman_absen_pulang($id)
    {
        $krs = DB::table('krs')
            ->select('krs.id as krs_id', 'krs.*', 'mahasiswa.nama as nama_mahasiswa', 'mata_kuliah.*', 'jurusan.nama as nama_jurusan', 'dosen.nama_lengkap as dosen')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->join('jurusan', 'jurusan.id', '=', 'mata_kuliah.jurusan_id')
            ->join('dosen', 'dosen.id', '=', 'mata_kuliah.dosen_id')
            ->where('krs.id', $id)->first();

        return view('pages.face-recogination.absen-pulang', [
            'krs' => $krs,
        ]);
    }

    public function daftar_wajah()
    {
        $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();

        return view('pages.face-recogination.regis-face', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function simpanDaftarWajah(Request $request)
    {
        try {
            $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();

            if ($mahasiswa->face_descriptor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wajah telah didaftarkan sebelumnya.',
                ], 422);
            }

            $mahasiswa->face_descriptor = json_encode($request->descriptor);
            $mahasiswa->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function descriptors()
    {
        $users = Mahasiswa::all();

        return response()->json($users->map(function ($user) {
            return [
                'name' => $user->nama,
                'descriptor' => json_decode($user->face_descriptor),
            ];
        }));
    }

    public function absen_masuk(Request $request)
    {
        $krs = $request->input('krs');

        $detail_krs = DB::table('krs')
            ->select('krs.*', 'mata_kuliah.waktu_mulai as waktu_mulai')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->where('krs.id', $krs)
            ->first();

        // dd($detail_krs);

        $latUser = $request->currentLatUser;
        $lngUser = $request->currentLngUser;

        // $latKelas = $request->currentLatUser;
        // $lngKelas = $request->currentLngUser;
        $latKelas = -5.375329714761104;
        $lngKelas = 105.24604359669844;

        $radius = round($this->distance($latUser, $lngUser, $latKelas, $lngKelas)['meters']);

        if ($radius > 100) {
            return response()->json([
                'status' => 'error radius',
                'message' => 'Anda diluar radius',
            ], 403);
        }

        $waktuMulai = Carbon::createFromFormat('H:i:s', $detail_krs->waktu_mulai);
        $sekarang = Carbon::now();

        if ($sekarang->lt($waktuMulai)) {
            return response()->json([
                'status' => 'error belum mulai',
                'message' => 'Mata kuliah belum dimulai',
            ], 422);
        }

        DB::table('riwayat_absensi')->insert([
            'krs_id' => $krs,
            'absensi_masuk' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absen simpan',
        ], 200);
    }

    public function absen_pulang(Request $request)
    {
        $krs = $request->input('krs');

        $detail_krs = DB::table('krs')
            ->select('krs.*', 'mata_kuliah.waktu_selesai as waktu_selesai')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->where('krs.id', $krs)
            ->first();

        if (! $detail_krs) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data KRS tidak ditemukan',
            ], 404);
        }

        $latUser = $request->currentLatUser;
        $lngUser = $request->currentLngUser;
        $latKelas = -5.375329714761104;
        $lngKelas = 105.24604359669844;

        $radius = round($this->distance($latUser, $lngUser, $latKelas, $lngKelas)['meters']);

        if ($radius > 50) {
            return response()->json([
                'status' => 'error radius',
                'message' => 'Anda diluar radius',
            ], 403);
        }

        $waktuSelesai = Carbon::today()->setTimeFromTimeString($detail_krs->waktu_selesai);
        $sekarang = Carbon::now();

        if ($sekarang->lt($waktuSelesai)) {
            return response()->json([
                'status' => 'error belum pulang',
                'message' => 'Belum waktu pulang',
            ], 422);
        }

        $absensiTerakhir = DB::table('riwayat_absensi')
            ->where('krs_id', $krs)
            ->whereNull('absensi_keluar')
            ->latest('id')
            ->first();

        if (! $absensiTerakhir) {
            return response()->json([
                'status' => 'error sudah absen pulang',
                'message' => 'Anda sudah absen pulang',
            ], 422);
        }

        DB::table('riwayat_absensi')
            ->where('id', $absensiTerakhir->id)
            ->update([
                'absensi_keluar' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absen pulang berhasil',
        ], 200);
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles) * 60 * 1.1515;

        return ['meters' => $miles * 1609.344];
    }
}
