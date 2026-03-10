@extends('layouts.be')

@section('title', 'Mahasiswa')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Mahasiswa</h1>
        </div>


        <a href="{{ route('fakultas') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="card shadow mt-2">
            <div class="card-body">
                <form action="#" id="form_simpan" method="post">
                    @csrf

                    <div class="form-group">
                        <label for="">Nama Lengkap:</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukan nama lengkap">
                        <span class="text-danger error-text nama_error" style="font-size: 12px;"></span>
                    </div>


                    <div class="form-group">
                        <label for="">NPM:</label>
                        <input type="text" name="npm" class="form-control" placeholder="Masukan npm">
                    </div>

                    <div class="form-group">
                        <label for="">Jurusan:</label>
                        <select name="jurusan" id="jurusan" class="form-control"></select>
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
                url: '/internal/mahasiswa/simpan',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    // Bersihkan error text
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
                            window.location.href = "{{ route('mahasiswa') }}";
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
            // minimumInputLength: 3,
            ajax: {
                url: "/internal/mahasiswa/listJurusan",
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
    });
</script>
@endpush
