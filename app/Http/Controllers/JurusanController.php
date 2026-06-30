<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return view('pages.jurusan.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Jurusan::with(['fakultas'])->get();

            return datatables()->of($data)
                ->addColumn('fakultas', function ($data) {
                    return $data->fakultas->nama_fakultas;
                })
                ->addColumn('aksi', function ($data) {
                    $button = ' <a href="'.route('jurusan.edit', $data->id).'" class="badge bg-warning text-white"><i class="fas fa-sm fa-edit"></i> Edit</a>
                      <a href="#" class="badge bg-danger hapus text-white" data-id="'.$data->id.'"> <i class="fas fa-sm fa-trash-alt"></i> Hapus</a>';

                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi', 'fakultas'])
                ->toJson();
        }
    }

    public function tambah()
    {
        return view('pages.jurusan.tambah');
    }

    public function simpan(Request $request)
    {
        $jurusan = new Jurusan;
        $jurusan->fakultas_id = $request->fakultas;
        $jurusan->nama = $request->jurusan;
        $jurusan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil di tambahkan',
        ], 200);
    }

    public function edit($id)
    {
        $jurusan = Jurusan::find($id);

        return view('pages.jurusan.edit', [
            'jurusan' => $jurusan,
        ]);
    }

    public function update(Request $request)
    {
        $jurusan = Jurusan::find($request->id);
        $jurusan->nama = $request->jurusan;
        $jurusan->fakultas_id = $request->fakultas;
        $jurusan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil di ubah',
        ], 200);
    }

    public function hapus(Request $request)
    {
        $jurusan = Jurusan::find($request->id);

        if ($jurusan) {
            $jurusan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil di hapus',
            ]);
        }
    }

    public function listFakultas(Request $request)
    {
        $data = $request->has('q')
            ? Fakultas::where('nama_fakultas', 'LIKE', '%'.$request->q.'%')->get()
            : Fakultas::all();

        return response()->json($data->map(fn ($d) => ['id' => $d->id, 'text' => $d->nama_fakultas]));
    }

    public function listFakultasByJurusan(Request $request)
    {
        $fakultas = Fakultas::whereHas('jurusan', function ($q) use ($request) {
            $q->where('id', $request->jurusan_id);
        })->get();

        return response()->json($fakultas);
    }
}
