@extends('layouts.be')

@section('title', 'Jurusan')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Jurusan</h1>
        </div>


        <a href="{{ route('jurusan') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="card shadow mt-2">
            <div class="card-body">
                <form action="#" id="form_simpan" method="post">
                    @csrf

                    <input type="hidden" name="id" class="form-control" id="jurusan_id" value="{{ $jurusan->id }}">

                    <div class="form-group">
                        <label for="">Jurusan:</label>
                        <input type="text" name="jurusan" class="form-control" placeholder="Masukan nama jurusan" value="{{ $jurusan->nama }}">
                        <span class="text-danger error-text jurusan_error" style="font-size: 12px;"></span>
                    </div>


                    <div class="form-group">
                        <label for="">Fakultas</label>
                        <select name="fakultas" class="form-control-range" id="fakultas"></select>
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
                url: '/internal/jurusan/update',
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
                            window.location.href = "{{ route('jurusan') }}";
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

        const jurusan_id = $('#jurusan_id').val();

        $.ajax({
            type: 'GET',
            url: "{{ route('jurusan.listFakultasByJurusan') }}",
            data: {
                jurusan_id: jurusan_id
            }
        }).then(function(data) {
            for (i = 0; i < data.length; i++) {
                var newOption = new Option(data[i].nama_fakultas, data[i].id, true,
                    true);

                $('#fakultas').append(newOption).trigger('change');
            }
        });

        $('#fakultas').select2({
            placeholder: '--Pilih Fakultas',
            width: '100%',
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: "/internal/jurusan/listFakultas",
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
