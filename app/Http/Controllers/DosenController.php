<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class DosenController extends Controller
{
    public function index()
    {
        return view('pages.dosen.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Dosen::with(['jurusan'])->get();

            return datatables()->of($data)
                ->addColumn('jurusan', function ($data) {
                    return $data->jurusan->nama;
                })
                ->addColumn('aksi', function ($data) {
                    $button = ' <a href="' . route('dosen.edit', $data->id) . '" class="badge bg-warning text-white"><i class="fas fa-sm fa-edit"></i> Edit</a>
                      <a href="#" class="badge bg-danger hapus text-white" data-id="' . $data->id . '"> <i class="fas fa-sm fa-trash-alt"></i> Hapus</a>';

                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi', 'fakultas'])
                ->toJson();
        }
    }

    public function tambah()
    {
        return view('pages.dosen.tambah');
    }


    public function listJurusan(Request $request)
    {
        if ($request->has('q')) {
            $search = $request->q;

            $jurusan = Jurusan::where('nama', 'LIKE', '%' . $search . '%')->get();

            $result = $jurusan->map(function ($data) {
                return [
                    'id' => $data->id,
                    'text' => $data->nama
                ];
            });

            return response()->json($result);
        } else {

            $jurusan = Jurusan::all();

            $result = $jurusan->map(function ($data) {
                return [
                    'id' => $data->id,
                    'text' => $data->nama
                ];
            });

            return response()->json($result);
        }
    }


    public function simpan(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required',
            'jurusan' => 'required'
        ], [
            'nama_lengkap.required' => 'nama lengkap wajib disi',
            'jurusan.required' => 'jurusan wajib di isi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'errors validation',
                'errors' => $validator->errors()
            ], 422);
        }


        $dosen = new Dosen();
        $dosen->nama_lengkap = $request->nama_lengkap;
        $dosen->nid = $request->nid;
        $dosen->jurusan_id = $request->jurusan;
        $dosen->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ], 200);
    }

    public function edit($id)
    {

        $dosen = Dosen::find($id);

        return view('pages.dosen.edit', [
            'dosen' => $dosen
        ]);
    }


    public function update(Request $request)
    {
        $dosen = Dosen::find($request->id);
        $dosen->nama_lengkap = $request->nama_lengkap;
        $dosen->nid = $request->nid;
        $dosen->jurusan_id = $request->jurusan;
        $dosen->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ], 200);
    }

    public function hapus(Request $request)
    {
        $dosen = Dosen::find($request->id);

        $dosen->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 200);
    }
}
