<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    public function index()
    {
        return view('pages.fakultas.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Fakultas::all();

            return datatables()->of($data)
                ->addColumn('aksi', function ($data) {
                    $button = ' <a href="#" class="badge bg-warning text-white"><i class="fas fa-sm fa-edit"></i> Edit</a>
                      <a href="#" class="badge bg-danger hapus text-white" data-id="' . $data->id . '"> <i class="fas fa-sm fa-trash-alt"></i> Hapus</a>';

                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi'])
                ->toJson();
        }
    }


    public function tambah()
    {
        return view('pages.fakultas.tambah');
    }


    public function simpan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required'
        ], [
            'nama_fakultas.required' => 'nama fakultas wajib di isi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'errors validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $fakultas = new Fakultas();
        $fakultas->nama_fakultas = $request->nama_fakultas;
        $fakultas->save();

        if ($fakultas) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambah',
            ], 200);
        }
    }

    public function edit($id)
    {
        $fakultas = Fakultas::find($id);

        return view('pages.fakultas.edit', [
            'fakultas' => $fakultas
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required'
        ], [
            'nama_fakultas.required' => 'nama fakultas wajib di isi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $fakultas = Fakultas::find($request->id);
        $fakultas->nama_fakultas = $request->nama_fakultas;
        $fakultas->save();

        if ($fakultas) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambah',
            ], 200);
        }
    }

    public function hapus(Request $request)
    {
        $fakultas = Fakultas::find($request->id);

        if ($fakultas->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di hapus'
            ]);
        }
    }
}
