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
        $krs = DB::table('krs')->where('id', $id)->first();

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

        if ($request->tipe == 'masuk') {
            $result =    DB::table('riwayat_absensi')
                ->insert([
                    'krs_id' => $request->krs,
                    'absensi_masuk' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]);

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil simpan'
                ]);
            }
        }

        if ($request->tipe === "pulang") {


            $riwayat_absensi_terbaru = DB::table('riwayat_absensi')->where('krs_id', $request->krs)->orderBy('created_at', 'DESC')->first();

            DB::table('riwayat_absensi')
                ->where('id', $riwayat_absensi_terbaru->id)
                ->update([
                    'absensi_keluar' => Carbon::now()
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambah'
            ]);
        }
    }
}
