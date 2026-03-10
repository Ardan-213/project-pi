<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KrsController extends Controller
{
    function tentukanSemester($bulan, $tahun)
    {
        // Semester ganjil: Agustus (8) sampai Januari (1)
        // Semester genap: Februari (2) sampai Juli (7)

        if ($bulan >= 8 && $bulan <= 12) {
            return [
                'nama' => 'Ganjil',
                'tahun_akademik' => $tahun . '/' . ($tahun + 1)
            ];
        } elseif ($bulan >= 1 && $bulan <= 1) {
            // Januari dianggap semester ganjil juga
            return [
                'nama' => 'Ganjil',
                'tahun_akademik' => ($tahun - 1) . '/' . $tahun
            ];
        } else {
            return [
                'nama' => 'Genap',
                'tahun_akademik' => ($bulan <= 7 ? ($tahun - 1) : $tahun) . '/' . $tahun
            ];
        }
    }


    public function index()
    {
        return view('pages.krs.index');
    }

    public function data(Request $request)
    {

        if ($request->ajax()) {
            $sekarang = Carbon::now();
            $bulan = (int) $sekarang->format('m');
            $tahun = (int) $sekarang->format('Y');

            $semesterInfo = $this->tentukanSemester($bulan, $tahun);

            $namaSemester = $semesterInfo['nama']; // 'Ganjil' atau 'Genap'
            $tahunAkademik = $semesterInfo['tahun_akademik']; // '2024/2025', dll

            $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();

            $data = Krs::with(['mata_kuliah'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('semester', $namaSemester)
                ->where('tahun', $tahunAkademik)
                ->get();


            return datatables()->of($data)
                ->addColumn('nama_mata_kuliah', function ($data) {
                    return $data->mata_kuliah->nama_mata_kuliah;
                })
                ->addColumn('dosen_pengajar', function ($data) {
                    return $data->mata_kuliah->dosen->nama_lengkap;
                })
                ->addColumn('jurusan', function ($data) {
                    return $data->mata_kuliah->jurusan->nama;
                })
                ->addColumn('sks', function ($data) {
                    return $data->mata_kuliah->sks;
                })
                ->addColumn('ruangan', function ($data) {
                    return $data->mata_kuliah->ruangan;
                })
                ->addColumn('hari', function ($data) {
                    return $data->mata_kuliah->hari;
                })
                ->addColumn('waktu_mulai', function ($data) {
                    return $data->mata_kuliah->waktu_mulai;
                })
                ->addColumn('waktu_selesai', function ($data) {
                    return $data->mata_kuliah->waktu_selesai;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '
        <div class="d-flex justify-content-start">

            <a href="#" class="badge bg-danger text-white ambil-mk hapus" data-id="' . $data->id . '">
                <i class="fas fa-sm fa-trash-alt"></i> Hapus
            </a>

                 <a href="' . route('halaman_deteksi_absensi', $data->id) . '" class="badge bg-info mx-1 text-white ambil-mk" data-id="' . $data->id . '">
                <i class="fas fa-sm fa-camera"></i> Absensi
            </a>

               <a href="#" class="badge bg-warning text-white riwayat_absen" data-id="' . $data->id . '">
                <i class="fas fa-sm fa-eye"></i> Lihat Riwayat Absensi
            </a>
        </div>';
                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi', 'nama_mata_kuliah', 'dosen_pengajar', 'sks', 'jurusan'])
                ->toJson();
        }
    }

    public function dataRiwayatAbsensi(Request $request)
    {
        // dd($request->all());
        $data = DB::table('riwayat_absensi')
            ->select('riwayat_absensi.*')
            ->where('krs_id', $request->krs)
            ->get();

        // dd($data);

        return datatables()->of($data)
            ->addIndexColumn()
            ->toJson();
    }


    public function inputKrs(Request $request)
    {
        $sekarang = Carbon::now();
        $bulan = (int) $sekarang->format('m');
        $tahun = (int) $sekarang->format('Y');

        $semesterInfo = $this->tentukanSemester($bulan, $tahun);

        $namaSemester = $semesterInfo['nama']; // 'Ganjil' atau 'Genap'
        $tahunAkademik = $semesterInfo['tahun_akademik']; // '2024/2025', dll

        $mahasiswa = Mahasiswa::where('users_id', Auth::user()->id)->first();


        // Ambil data mata kuliah
        $mataKuliah = MataKuliah::find($request->mata_kuliah_id);

        // Cek apakah kuota masih tersedia
        if ($mataKuliah->kuota_orang <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuota mata kuliah sudah habis'
            ], 400);
        }

        $mataKuliah->decrement('kuota_orang');

        $krs = new Krs();
        $krs->mahasiswa_id = $mahasiswa->id;
        $krs->mata_kuliah_id = $request->mata_kuliah_id;
        $krs->semester = $namaSemester;
        $krs->tahun = $tahunAkademik;
        $krs->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ], 200);
    }


    public function hapus(Request $request)
    {
        $krs = Krs::find($request->id);

        $mataKuliah = $krs->mata_kuliah;

        if ($mataKuliah) {
            $mataKuliah->increment('kuota_orang');
        }

        $krs->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
