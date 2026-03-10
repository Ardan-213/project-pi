<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->when(request()->q, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->q . '%');
        })->paginate(5);

        return view('pages.role.index', [
            'roles' => $roles
        ]);
    }

    public function tambah()
    {
        $permissions = Permission::latest()->get();

        return view('pages.role.tambah', [
            'permissions' => $permissions
        ]);
    }

    public function simpan(Request $request)
    {

        // dd($request->all());
        $this->validate($request, [
            'name' => 'required|unique:roles'
        ]);

        $role = Role::create([
            'name' => strtolower($request->input('name'))
        ]);

        //assign permission to role
        $role->syncPermissions($request->input('permissions'));

        if ($role) {
            //redirect dengan pesan sukses
            return redirect()->route('role')->with('status', 'Data berhasil ditambah');
        }
    }


    public function hapus($id) {
        $role = Role::find($id);

        $role->delete();

         return redirect()->route('role')->with('status', 'Data berhasil dihapus');
    }
}
