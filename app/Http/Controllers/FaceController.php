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
    public function halaman_deteksi_absensi($id)
    {
        $krs = DB::table('krs')
            ->select('krs.*', 'mahasiswa.nama as nama_mahasiswa')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->where('krs.id', $id)->first();

        return view('pages.face-recogination.absensi-detect-wajah', [
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

    public function absensi(Request $request)
    {

        $krs = $request->input('krs');

        $detail_krs = DB::table('krs')
            ->select('krs.*', 'mata_kuliah.waktu_mulai as waktu_mulai')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs.mata_kuliah_id')
            ->where('krs.id', $krs)
            ->first();


        $currentLatUser = $request->currentLatUser;
        $currentLngUser = $request->currentLngUser;


        /*
        |--------------------------------------------------------------------------
        | Lat Long Asli Pasca UBL

        lat = -5.375329714761104
        long = 105.24604359669844
        |--------------------------------------------------------------------------
        */
        $latAbsen =  -5.375329714761104;
        $langAbsen =  105.24604359669844;


        $jarak = $this->distance($latAbsen, $langAbsen, $currentLatUser, $currentLngUser);

        $radius = round($jarak['meters']);


        if ($radius > 50) {
            return response()->json([
                'status' => 'error radius',
                'message' => 'Anda diluar radius'
            ], 400);
        }

        $waktuMulai = Carbon::createFromFormat('H:i:s', $detail_krs->waktu_mulai);
        $sekarang   = Carbon::now();

        // batas toleransi (10 menit)
        $batasMasuk = $waktuMulai->copy()->addMinutes(10);

        if ($sekarang->timestamp <= $batasMasuk->timestamp) {
            //  masih boleh absen (on time / toleransi)
            $status = 'tepat waktu';
        } else {
            return response()->json([
                'status' => 'error terlambat',
                'message' => 'Absen simpan'
            ], 400);
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


    function distance($lat1, $lon1, $lat2, $lon2)
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
