@extends('layouts.be')

@section('title', 'Mata Kuliah')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Mata Kuliah</h1>
        </div>


        <a href="{{ route('mata-kuliah') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="card shadow mt-2">
            <div class="card-body">
                <form action="#" id="form_simpan" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Kode:</label>
                                <input type="text" name="kode" class="form-control" placeholder="Masukan kode mata kuliah">
                                <span class="text-danger error-text jurusan_error" style="font-size: 12px;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Mata Kuliah:</label>
                                <input type="text" name="nama_mata_kuliah" class="form-control" placeholder="Masukan mata kuliah">
                                <span class="text-danger error-text jurusan_error" style="font-size: 12px;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Jurusan</label>
                                <select name="jurusan" class="form-control-range" id="jurusan"></select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dosen</label>
                                <select name="dosen" class="form-control" id="dosen" disabled>
                                    <option value="">--Pilih Jurusan Dahulu--</option>
                                </select>
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">SKS:</label>
                                <input type="text" name="sks" class="form-control" placeholder="Masukan sks">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Ruangan:</label>
                                <input type="text" name="ruangan" class="form-control" placeholder="Masukan ruangan">
                            </div>
                        </div>

                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Kuota Orang:</label>
                                <input type="number" class="form-control" name="kuota_orang">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Hari:</label>
                                <select name="hari" id="" class="form-control">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Waktu Mulai:</label>
                                <input type="time" class="form-control" name="waktu_mulai">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="">Waktu Selesai:</label>
                            <input type="time" class="form-control" name="waktu_selesai">
                        </div>
                    </div>



                    <button class="btn btn-sm btn-primary" type="submit">
                        Simpan
                    </button>

                </form>
            </div>
        </div>

    </section>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $("#form_simpan").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '/internal/mata-kuliah/simpan',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('mata-kuliah') }}";
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    }
                }
            });
        });

        $('#jurusan').select2({
            placeholder: '--Pilih Jurusan--',
            width: '100%',
            allowClear: true,
            ajax: {
                url: "/internal/mata-kuliah/listJurusan",
                dataType: 'json',
                delay: 500,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.text,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        $('#jurusan').on('change', function() {
            let jurusan_id = $(this).val();

            $('#dosen').removeAttr('disabled');

            $('#dosen').trigger('change').val('');

            $('#dosen').select2({
                placeholder: '--Pilih Dosen--',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: "/internal/mata-kuliah/listDosenByJurusan",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            jurusan_id: jurusan_id
                        };
                    },
                    delay: 500,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });


        });
    });
</script>
@endpush
