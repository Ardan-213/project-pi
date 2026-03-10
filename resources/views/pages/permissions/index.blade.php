@extends('layouts.be')

@section('title', 'Fakultas')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Permissions</h1>
        </div>

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-key"></i> Permissions</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('permission') }}" method="GET">
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
                                    <th scope="col">NAMA PERMISSION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ( $permissions as $no => $permission )
                                <tr>
                                    <th scope="row" style="text-align: center">{{ ++$no + ($permissions->currentPage()-1) * $permissions->perPage() }}</th>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">Data Kosong</td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                        <div style="text-align: center">
                            {{ $permissions->links() }}
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
