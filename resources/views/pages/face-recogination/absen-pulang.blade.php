@extends('layouts.be')

@section('title', 'Absensi Pulang')
@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Halaman Absensi Pulang</h1>
        </div>

        <a href="{{ route('krs') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-sm fa-arrow-left"></i> Kembali
        </a>

        <div class="row">
            <div class="col-md-5">
                <div class="card shadow mt-2">
                    <div class="card-body">
                        <div class="center-container">
                            <input type="hidden" id="lokasi">

                            <div id="video-container">
                                <video id="video" data-krs="{{ $krs->krs_id }}" data-nama="{{ $krs->nama_mahasiswa }}" autoplay muted></video>
                            </div>

                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="width: 100%;">
                                <tr>
                                    Waktu Saat ini:
                                </tr>
                                <td>
                                    <div class="clock" id="liveClock"></div>
                                </td>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <td>{{ $krs->nama_mata_kuliah }}</td>
                                </tr>
                                <tr>
                                    <th>Dosen</th>
                                    <td>{{ $krs->dosen }}</td>
                                </tr>
                                <tr>
                                    <th>Jurusan</th>
                                    <td>{{ $krs->nama_jurusan }}</td>
                                </tr>
                                <tr>
                                    <th>SKS</th>
                                    <td>{{ $krs->sks }}</td>
                                </tr>
                                <tr>
                                    <th>Ruangan</th>
                                    <td>{{ $krs->ruangan }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Mulai</th>
                                    <td>{{ $krs->waktu_mulai }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Selesai</th>
                                    <td>{{ $krs->waktu_selesai }}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

@endsection

@push('style')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map {
        margin-top: 25px;
        width: 100%;
        height: 300px;
        overflow: hidden;
    }

    .center-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    #video-container {
        position: relative;
        width: 100%;
        margin-top: 20px;
    }

    #video-container video {
        width: 100%;
        height: auto;
        display: block;
        max-height: 360px;
    }



    .clock {
        font-size: 1.5rem;
        font-weight: bold;
        font-family: monospace;
        color: #34395e;
    }

    #video-container canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
</style>
@endpush

@push('script')
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script defer src="/js/absen-pulang.js"></script>
<script>
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        document.getElementById('liveClock').textContent = time;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@endpush