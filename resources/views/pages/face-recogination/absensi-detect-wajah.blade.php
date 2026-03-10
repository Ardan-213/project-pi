<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deteksi Wajah</title>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="/js/face-recognition.js"></script> <!-- file JS yang akan kamu isi -->
</head>

<body>

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
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
        <div id="video-container">
            <video id="video" width="640" data-krs="{{ $krs->id }}" height="480" autoplay muted></video>
        </div>

        <div class="button-container">
            <button id="absenMasuk" disabled>Absen Masuk</button>
            <button id="absenPulang" disabled>Absen Pulang</button>
        </div>
</body>

</html>
