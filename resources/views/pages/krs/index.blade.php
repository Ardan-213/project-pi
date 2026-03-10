@extends('layouts.be')

@section('title', 'KRS')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>KRS</h1>
        </div>


        <a href="{{ route('mata-kuliah') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-plus-circle"></i> Input KRS
        </a>

        <div class="card shadow mt-2">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="width: 100%;" id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen Pengajar</th>
                                <th>Jurusan</th>
                                <th>SKS</th>
                                <th>Ruangan</th>
                                <th>Hari</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>

    </section>
</div>

<div class="modal fade" id="modalRiwayatAbsensi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Riwayat Absensi</h5>

            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="width: 100%;" id="tableRiwayatMasuk">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Absen Masuk</th>
                                <th>Absen Pulang</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
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
            searching: false,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 20, 25, -1],
                [10, 20, 25, "50"]
            ],

            order: [],
            ajax: {
                url: "{{ route('krs.data') }}",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    'orderable': false,
                    'searchable': false
                },
                {
                    data: 'nama_mata_kuliah',
                    name: 'nama_mata_kuliah'
                },

                {
                    data: 'dosen_pengajar',
                    name: 'dosen_pengajar'
                },
                {
                    data: 'jurusan',
                    name: 'jurusan'
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
                    data: 'aksi',
                    name: 'aksi'
                },
            ]
        });

        function datatableRiwayatAbsen(krs=null){
                $("#tableRiwayatMasuk").DataTable({
                destroy: true,
                processing: true,
                searching:false,
                searching: false,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 20, 25, -1],
                    [10, 20, 25, "50"]
                ],

                order: [],
                ajax: {
                    url: "{{ route('dataRiwayatAbsensi') }}",
                    data:{
                        krs: krs
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        'orderable': false,
                        'searchable': false
                    },
                      {
                        data: 'absensi_masuk',
                        name: 'absensi_masuk'
                    },
                    {
                        data: 'absensi_keluar',
                        name: 'absensi_keluar'
                    },

                ]
            });
        }


        $(document).on('click', '.riwayat_absen', function() {
            $('#modalRiwayatAbsensi').modal('show');

            const krs = $(this).data('id');

            datatableRiwayatAbsen(krs);
        })

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
                        url: "{{ route('krs.hapus') }}",
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
