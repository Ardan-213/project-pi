@extends('layouts.be')

@section('title', 'Role')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Role</h1>
        </div>

        @if (session('status'))
        <div class="alert alert-custom">
            {{ session('status') }}
        </div>
        @endif

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4>Role</h4>
                    <div class="card-header-action">
                        <a href="{{ route('role.tambah') }}" class="btn btn-primary">
                            <i class="fas fa-sm fa-plus-circle"></i> Tambah
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('role') }}" method="GET">
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="q"
                                    placeholder="cari berdasarkan nama permissions">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> CARI
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" style="text-align: center;width: 6%">NO.</th>
                                    <th scope="col">ROLE</th>
                                    <th scope="col">Permissions</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ( $roles as $no => $role )
                                <tr>
                                    <th scope="row" style="text-align: center">{{ ++$no + ($roles->currentPage()-1) * $roles->perPage() }}</th>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach($role->getPermissionNames() as $permission)
                                        <button class="btn btn-sm btn-primary text-white mb-1 mt-1 mr-1">{{ $permission }}</button>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            @if ($role->name == 'super admin' || $role->name == 'Super admin')
                                            -

                                            @else

                                            <a href="{{ route('role.hapus' , $role->id) }}" class="btn btn-sm btn-danger mx-1 text-white">
                                                <i class="fas fa-sm fa-trash"></i> Hapus
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Data Kosong</td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                        <div style="text-align: center">
                            {{ $roles->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

@endsection

@push('style')

<style>
    .alert-custom {
        background-color: #2F5249;
        color: white;
    }

    .dt-type-numeric {
        text-align: start !important;
    }

    label {
        margin-left: 10px !important;
    }

    .table-bordered {
        border: 2px solid #dee2e6 !important;
    }

    .table-bordered th,
    .table-bordered td {
        border: 2px solid #dee2e6 !important;
    }

    .dt-paging-button.current {
        background-color: #6777ef !important;
        /* Ganti sesuai warna yang kamu inginkan */
        color: white !important;
        /* Agar teks tetap terlihat */
        border-radius: 4px !important;
        /* Opsional: biar agak rounded */
        border: none !important;
        /* Opsional: hapus border bawaan */
    }

    div.dt-container .dt-paging .dt-paging-button.current,
    div.dt-container .dt-paging .dt-paging-button.current:hover {
        color: white !important;
    }
</style>
@endpush
