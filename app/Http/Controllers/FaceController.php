<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaceController extends Controller
{
    public function halaman_absen_masuk($id)
    {
        $krs = DB::table('krs')
            ->select('krs.*', 'mahasiswa.nama as nama_mahasiswa')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->where('krs.id', $id)->first();

        return view('pages.face-recogination.absen-masuk', [
            'krs' => $krs
        ]);
    }

      public function halaman_absen_pulang($id)
    {
        $krs = DB::table('krs')
            ->select('krs.*', 'mahasiswa.nama as nama_mahasiswa')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->where('krs.id', $id)->first();

        return view('pages.face-recogination.absen-pulang', [
            'krs' => $krs
        ]);
    }


    public function daftar_wajah()
    {
        $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();

        return view('pages.face-recogination.regis-face', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function simpanDaftarWajah(Request $request)
    {

        try {
            $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();


            $mahasiswa = Mahasiswa::find($mahasiswa->id);
            $mahasiswa->face_descriptor = json_encode($request->descriptor);
            $mahasiswa->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function descriptors()
    {
        $users = Mahasiswa::all();
        return response()->json($users->map(function ($user) {
            return [
                'name' => $user->nama,
                'descriptor' => json_decode($user->face_descriptor)
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
        $currentLatUser = $request->currentLatUser;
        $currentLngUser = $request->currentLngUser;

        // $latAbsen =  -5.375329714761104;
        // $langAbsen =  105.24604359669844;

        $latAbsen = $request->currentLatUser;
        $langAbsen =  $request->currentLngUser;


        $jarak = $this->distance($latAbsen, $langAbsen, $currentLatUser, $currentLngUser);

        $radius = round($jarak['meters']);


        if ($radius > 50) {
            return response()->json([
                'status' => 'error radius',
                'message' => 'Anda diluar radius'
            ], 403);
        }

        $waktuMulai = Carbon::createFromFormat('H:i:s', $detail_krs->waktu_mulai);
        $sekarang   = Carbon::now();

        // Jika sebelum jam mulai
        if ($sekarang->lt($waktuMulai)) {
            return response()->json([
                'status' => 'error belum mulai',
                'message' => 'Mata kuliah belum dimulai'
            ], 422);
        }

        // batas toleransi (10 menit)
        $batasMasuk = $waktuMulai->copy()->addMinutes(10);

        if ($sekarang->timestamp <= $batasMasuk->timestamp) {
            $status = 'tidak terlambat';
        } else {
            return response()->json([
                'status' => 'error terlambat',
                'message' => 'Data ditolak karena lebih dari toleransi terlambat'
            ], 422);
        }


        $result =    DB::table('riwayat_absensi')
            ->insert([
                'krs_id' => $krs,
                'absensi_masuk' => Carbon::now(),
                'created_at' => Carbon::now(),
                'status' => $status
            ]);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'Absen simpan'
            ], 200);
        }
    }


    public function absen_pulang(Request $request)
    {

        $krs = $request->input('krs');

        $detail_krs = DB::table('krs')
            ->select('krs.*', 'mata_kuliah.waktu_selesai as waktu_selesai')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->where('krs.id', $krs)
            ->first();

        $currentLatUser = $request->currentLatUser;
        $currentLngUser = $request->currentLngUser;

        // $latAbsen =  -5.375329714761104;
        // $langAbsen =  105.24604359669844;

        $latAbsen = $request->currentLatUser;
        $langAbsen =  $request->currentLngUser;


        $jarak = $this->distance($latAbsen, $langAbsen, $currentLatUser, $currentLngUser);

        $radius = round($jarak['meters']);


        if ($radius > 50) {
            return response()->json([
                'status' => 'error radius',
                'message' => 'Anda diluar radius'
            ], 403);
        }

        $waktu_selesai = Carbon::createFromFormat('H:i:s', $detail_krs->waktu_selesai);
        $sekarang   = Carbon::now();

        // Jika sekarang masih sebelum jam selesai
        if ($sekarang->lt($waktu_selesai)) {
            return response()->json([
                'status' => 'error belum pulang',
                'message' => 'Belum waktu pulang'
            ], 422);
        }


        $absensiTerakhir = DB::table('riwayat_absensi')
            ->where('krs_id', $krs)
            ->whereNull('absensi_pulang')
            ->latest('id')
            ->first();

        if (!$absensiTerakhir) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absen pulang'
            ], 422);
        }

        $result =    DB::table('riwayat_absensi')
            ->where('id', $absensiTerakhir->id)
            ->update([
                'absensi_pulang' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'Absen simpan'
            ], 200);
        }
    }


    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
