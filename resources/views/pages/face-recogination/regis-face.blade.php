@extends('layouts.be')

@section('title', 'Registrasi Wajah')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Registrasi Wajah</h1>
        </div>

        <a href="{{ route('krs') }}" class="btn btn-sm btn-primary mb-3">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mt-2">
                    <div class="card-body text-center">
                        <div class="center-container">
                            <div id="video-container">
                                <video id="video" data-nama="{{ $mahasiswa->nama }}" data-npm="{{ $mahasiswa->npm }}" autoplay muted></video>
                            </div>

                            <div class="button-container mt-3">
                                <button class="btn btn-primary" onclick="registerFace()">
                                    <i class="fas fa-camera"></i> Daftarkan Wajah
                                </button>
                            </div>

                            <div id="status" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mt-2">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Mahasiswa</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama</th>
                                <td>{{ $mahasiswa->nama }}</td>
                            </tr>
                            <tr>
                                <th>NPM</th>
                                <td>{{ $mahasiswa->npm }}</td>
                            </tr>
                        </table>

                        <hr>

                        <h5>Petunjuk</h5>
                        <ul class="pl-3">
                            <li>Pastikan pencahayaan cukup</li>
                            <li>Hadapkan wajah penuh ke kamera</li>
                            <li>Hindari aksesoris yang menutupi wajah</li>
                            <li>Klik tombol <strong>Daftarkan Wajah</strong> untuk menyimpan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('style')
<style>
    .center-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    #video-container {
        position: relative;
        width: 100%;
    }

    #video-container video {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
    }

    #video-container canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .button-container {
        width: 100%;
    }

    .button-container .btn {
        width: 100%;
        padding: 10px;
        font-size: 1.1rem;
    }
</style>
@endpush

@push('script')
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script defer src="/js/register.js"></script>
@endpush