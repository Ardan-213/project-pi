@extends('layouts.be')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Users</h1>
        </div>

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-users"></i> Users</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" style="text-align: center;width: 6%">NO.</th>
                                    <th scope="col">NAMA USER</th>
                                    <th scope="col">USERNAME</th>
                                    <th scope="col">ROLE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $no => $user)
                                <tr>
                                    <th scope="row" style="text-align: center">{{ ++$no + ($users->currentPage()-1) * $users->perPage() }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $role)
                                        <label class="badge badge-primary">{{ $role }}</label>
                                        @endforeach
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="text-align: center">
                            {{$users->links()}}
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
