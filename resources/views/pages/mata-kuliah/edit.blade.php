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
                                <input type="text" name="kode" class="form-control" placeholder="Masukan kode mata kuliah" value="{{ $mata_kuliah->kode }}">
                                <span class="text-danger error-text kode_error" style="font-size: 12px;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Mata Kuliah:</label>
                                <input type="text" name="nama_mata_kuliah" class="form-control" placeholder="Masukan mata kuliah" value="{{ $mata_kuliah->nama_mata_kuliah }}">
                                <span class="text-danger error-text nama_mata_kuliah_error" style="font-size: 12px;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Jurusan</label>
                                <select name="jurusan" class="form-control-range" id="jurusan">
                                    <option value="{{ $mata_kuliah->jurusan_id }}" selected>{{ $mata_kuliah->jurusan->nama ?? '' }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dosen</label>
                                <select name="dosen" class="form-control" id="dosen">
                                    <option value="{{ $mata_kuliah->dosen_id }}" selected>{{ $mata_kuliah->dosen->nama_lengkap ?? '' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">SKS:</label>
                                <input type="text" name="sks" class="form-control" placeholder="Masukan sks" value="{{ $mata_kuliah->sks }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Ruangan:</label>
                                <input type="text" name="ruangan" class="form-control" placeholder="Masukan ruangan" value="{{ $mata_kuliah->ruangan }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Kuota Orang:</label>
                                <input type="number" class="form-control" name="kuota_orang" value="{{ $mata_kuliah->kuota_orang }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Hari:</label>
                                <select name="hari" class="form-control">
                                    <option value="Senin" {{ $mata_kuliah->hari == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ $mata_kuliah->hari == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ $mata_kuliah->hari == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ $mata_kuliah->hari == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ $mata_kuliah->hari == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Waktu Mulai:</label>
                                <input type="time" class="form-control" name="waktu_mulai" value="{{ $mata_kuliah->waktu_mulai }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="">Waktu Selesai:</label>
                            <input type="time" class="form-control" name="waktu_selesai" value="{{ $mata_kuliah->waktu_selesai }}">
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
        var mataKuliahId = {{ $mata_kuliah->id }};
        var existingJurusanId = {{ $mata_kuliah->jurusan_id ?? 'null' }};
        var existingDosenId = {{ $mata_kuliah->dosen_id ?? 'null' }};

        $("#form_simpan").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '/internal/mata-kuliah/update/' + mataKuliahId,
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

        $('#dosen').select2({
            placeholder: '--Pilih Dosen--',
            width: '100%',
            allowClear: true,
            ajax: {
                url: "/internal/mata-kuliah/listDosenByJurusan",
                dataType: 'json',
                data: function(params) {
                    var jurusanId = $('#jurusan').val() || existingJurusanId;
                    return {
                        q: params.term,
                        jurusan_id: jurusanId
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

        $('#jurusan').on('change', function() {
            var jurusanId = $(this).val();
            $('#dosen').val('').trigger('change');
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
                            jurusan_id: jurusanId
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
