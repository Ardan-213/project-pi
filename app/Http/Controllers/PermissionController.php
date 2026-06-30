<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->when(request()->q, function ($permissions) {
            $permissions = $permissions->where('name', 'like', '%'.request()->q.'%');
        })->paginate(5);

        return view('pages.permissions.index', [
            'permissions' => $permissions,
        ]);
    }
}
