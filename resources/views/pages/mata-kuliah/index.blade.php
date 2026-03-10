@extends('layouts.be')

@section('title', 'Mata Kuliah')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Mata Kuliah</h1>
        </div>


        @can('akses tambah mata kuliah')
        <a href="{{ route('mata-kuliah.tambah') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-plus-circle"></i> Tambah
        </a>
        @endcan

        <div class="card shadow mt-2">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="width: 100%;" id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th>Jurusan</th>
                                <th>Dosen</th>
                                <th>SKS</th>
                                <th>Ruangan</th>
                                <th>Hari</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Kuota Orang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
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

@push('script')
<script>
    $(document).ready(function() {

        $('#datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 20, 25, -1],
                [10, 20, 25, "50"]
            ],

            order: [],
            ajax: {
                url: "{{ route('mata-kuliah.data') }}",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    'orderable': false,
                    'searchable': false
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'nama_mata_kuliah',
                    name: 'nama_mata_kuliah'
                },
                {
                    data: 'jurusan',
                    name: 'jurusan'
                },

                {
                    data: 'dosen',
                    name: 'dosen'
                },
                {
                    data: 'sks',
                    name: 'sks'
                },
                {
                    data: 'ruangan',
                    name: 'ruangan'
                },
                {
                    data: 'hari',
                    name: 'hari'
                },
                {
                    data: 'waktu_mulai',
                    name: 'waktu_mulai'
                },
                {
                    data: 'waktu_selesai',
                    name: 'waktu_selesai'
                },
                {
                    data: 'kuota_orang',
                    name: 'kuota_orang'
                },


                {
                    data: 'aksi',
                    name: 'aksi'
                },
            ]
        });

        $(document).on('click', '.ambil_mata_kuliah', function() {
            let id = $(this).attr('data-id');

            Swal.fire({
                title: 'Ambil Mata Kuliah?',
                text: "Data akan tersimpan",
                icon: 'warning',
                confirmButton: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ambil!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('krs.inputKrs') }}",
                        data: {
                            mata_kuliah_id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res, status) {
                            if (status = '200') {
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Data telah Disimpan',
                                    title: 'Berhasil',
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                $('#datatable').DataTable().ajax.reload();
                            }
                        },
                    })
                }
            });
        });
        $(document).on('click', '.hapus', function() {
            let id = $(this).attr('data-id');
            Swal.fire({
                title: 'Hapus data?',
                text: "Data akan terhapus!",
                icon: 'warning',
                confirmButton: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('fakultas.hapus') }}",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res, status) {
                            if (status = '200') {
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Data telah dihapus',
                                    title: 'Berhasil',
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                $('#datatable').DataTable().ajax.reload();
                            }
                        },
                    })
                }
            });
        });
    });
</script>
@endpush
