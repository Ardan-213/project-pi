<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deteksi Wajah</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin="" />


</head>

<body>

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        #map {
            margin-top: 25px;
            flex-grow: 1;
            width: 650px;
            height: 300px;
        }

        body {
            display: flex;
            margin-top: 40px;
            justify-content: center;
            align-items: flex-start;
            background-color: #f0f0f0;
        }

        .center-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #video-container {
            position: relative;
            display: inline-block;
            flex-shrink: 0;
        }

        .button-container {
            margin-top: 20px;
        }

        button {
            margin: 0 5px;
            padding: 8px 16px;
        }

        #video-container video,
        #video-container canvas {
            width: 100%;
            height: auto;
            display: block;
            margin-top: 20px;
        }

        #video-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            /* agar tidak mengganggu klik */
        }
    </style>
    <div class="center-container">

        <input type="hidden" class="form-control " id="lokasi">

        <div id="video-container">
            <video id="video" width="640" data-krs="{{ $krs->id }}" data-nama="{{ $krs->nama_mahasiswa }}" height="480" autoplay muted></video>
        </div>


        <div id="map"></div>



        <!-- <div class="button-container">
            <button id="absenMasuk">Absen Masuk</button>
            <button id="absenPulang">Absen Pulang</button>
        </div> -->


        <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
        <script defer src="/js/face-recognition.js"></script>

</body>

</html>
