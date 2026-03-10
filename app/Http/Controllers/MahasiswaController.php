<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MahasiswaController extends Controller
{

    public function index()
    {
        return view('pages.mahasiswa.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Mahasiswa::with(['jurusan'])->get();

            return datatables()->of($data)
                ->addColumn('jurusan', function ($data) {
                    return $data->jurusan->nama;
                })
                ->addColumn('aksi', function ($data) {
                    $button = ' <a href="#" class="badge bg-warning text-white"><i class="fas fa-sm fa-edit"></i> Edit</a>
                      <a href="#" class="badge bg-danger hapus text-white" data-id="' . $data->id . '"> <i class="fas fa-sm fa-trash-alt"></i> Hapus</a>';

                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['aksi', 'jurusan'])
                ->toJson();
        }
    }

    public function tambah(Request $request)
    {
        return view('pages.mahasiswa.tambah');
    }

    public function simpan(Request $request)
    {

        // dd($request->all());

        DB::beginTransaction();

        try {
            $mahasiswa = new Mahasiswa();
            $mahasiswa->nama = $request->nama;
            $mahasiswa->npm = $request->npm;
            $mahasiswa->jurusan_id = $request->jurusan;
            $mahasiswa->save();

            $password = "password";

            $role = Role::where('name', 'mahasiswa')
                ->orwHere('name', 'Mahasiswa')
                ->first();


            $user = new User();
            $user->name = $request->nama;
            $user->username = $request->npm;
            $user->password = Hash::make($password);
            $user->save();

            $user->assignRole($role);

            $mahasiswa->update([
                'users_id' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function listJurusan(Request $request)
    {
        if ($request->has('q')) {
            $search = $request->q;

            $data = Jurusan::where('nama', 'LIKE', '%' . $search . '%')->get();

            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama,
                ];
            });

            return response()->json($result);
        } else {
            $data = Jurusan::all();

            $result = $data->map(function ($d) {
                return [
                    'id' => $d->id,
                    'text' => $d->nama,
                ];
            });

            return response()->json($result);
        }
    }

    public function hapus(Request $request)
    {
        $mahasiswa = Mahasiswa::find($request->id);

        $mahasiswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
