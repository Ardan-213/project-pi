<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    public function index()
    {
        return view('pages.mata-kuliah.index');
    }


    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = MataKuliah::with(['jurusan', 'dosen'])->get();


            return datatables()->of($data)
                ->addColumn('jurusan', function ($data) {
                    return $data->jurusan->nama;
                })
                ->addColumn('dosen', function ($data) {
                    return $data->dosen->nama_lengkap;
                })
                ->addColumn('aksi', function ($data) {
                    if (Auth::user()->hasRole('mahasiswa')) {
                        $button = '
        <div class="d-flex justify-content-start">

            <a href="#" class="badge bg-primary text-white ambil-mk ambil_mata_kuliah" data-id="' . $data->id . '">
                <i class="fas fa-sm fa-pencil-alt"></i> Ambil Mata Kuliah
            </a>
        </div>';
                    } else {

                        $button = '
        <div class="d-flex justify-content-start">
            <a href="' . route('jurusan.edit', $data->id) . '" class="badge bg-warning text-white me-1">
                <i class="fas fa-sm fa-edit"></i> Edit
            </a>
            <a href="#" class="badge bg-danger text-white mx-1 hapus" data-id="' . $data->id . '">
                <i class="fas fa-sm fa-trash-alt"></i> Hapus
            </a>

        </div>';
                    }



                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi', 'jurusan', 'dosen'])
                ->toJson();
        }
    }


    public function tambah()
    {
        return view('pages.mata-kuliah.tambah');
    }

    public function simpan(Request $request)
    {

        // dd($request->all());
        // $validator = Validator::make($request->all(), [
        //     'kode' => 'required',
        //     'nama_mata_kuliah' => 'required',
        //     'jurusan' => 'required',
        //     'dosen' => 'required',
        //     'sks' => 'required',
        //     'ruangan' => 'required',
        //     'hari' => 'required',
        //     'waktu_mulai' => 'required',
        //     'waktu_selesai' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        $mata_kuliah = new MataKuliah();
        $mata_kuliah->kode = $request->kode;
        $mata_kuliah->nama_mata_kuliah = $request->nama_mata_kuliah;
        $mata_kuliah->jurusan_id = $request->jurusan;
        $mata_kuliah->dosen_id = $request->dosen;
        $mata_kuliah->sks = $request->sks;
        $mata_kuliah->ruangan = $request->ruangan;
        $mata_kuliah->hari = $request->hari;
        $mata_kuliah->waktu_mulai = $request->waktu_mulai;
        $mata_kuliah->waktu_selesai = $request->waktu_selesai;
        $mata_kuliah->kuota_orang = $request->kuota_orang;
        $mata_kuliah->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambah'
        ], 200);
    }

    public function listJurusan(Request $request)
    {
        if ($request->has('q')) {
            $search = $request->q;

            $data = Jurusan::where('nama', 'LIKE', '%' . $search . '%')->get();

            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama
                ];
            });

            return response()->json($result);
        } else {
            $data = Jurusan::all();


            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama
                ];
            });

            return response()->json($result);
        }
    }


    public function listDosenByJurusan(Request $request)
    {
        if ($request->has('q')) {
            $search = $request->q;

            $data = Dosen::where('jurusan_id', $request->jurusan_id)
                ->where('nama_lengkap', 'LIKE', '%' . $search . '%')
                ->get();

            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama_lengkap
                ];
            });

            return response()->json($result);
        } else {
            $data = Dosen::where('jurusan_id', $request->jurusan_id)->get();


            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama_lengkap
                ];
            });

            return response()->json($result);
        }
    }

    public function edit($id) {}

    public function update(Request $request) {}

    public function hapus(Request $request)
    {
        $mata_kuliah = MataKuliah::find($request->id);

        $mata_kuliah->delete();

        return response()->json([
            'status' => 'succcess',
            'messsage' => 'Data berhasil dihapus'
        ], 200);
    }
}
