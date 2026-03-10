@extends('layouts.be')

@section('title', 'Fakultas')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Fakultas</h1>
        </div>


        <a href="{{ route('fakultas') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="card shadow mt-2">
            <div class="card-body">
                <form action="#" id="form_simpan" method="post">
                    @csrf

                    <div class="form-group">
                        <label for="">Nama Fakultas:</label>
                        <input type="text" name="nama_fakultas" class="form-control" placeholder="Masukan nama fakultas">
                        <span class="text-danger error-text nama_fakultas_error" style="font-size: 12px;"></span>
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
    $(document).ready(function () {
    $("#form_simpan").submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: '/internal/fakultas/simpan',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                // Bersihkan error text
                $('span.error-text').text('');
            },
            success: function (response) {
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

                    setTimeout(function () {
                        window.location.href = "{{ route('fakultas') }}";
                    }, 1500);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                }
            }
        });
    });
});

</script>
@endpush
