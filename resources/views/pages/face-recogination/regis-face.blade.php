<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Wajah</title>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script defer src="/js/register.js"></script> <!-- file JS yang akan kamu isi -->
</head>

<body>

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        /* body {
            display: flex;
            justify-content: ;
            align-items: center;
            background-color: #f0f0f0;
        } */

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
        <h1>Registrasi Wajah</h1>
        <div id="video-container">
            <video id="video" width="640" height="480" autoplay muted></video>
            <!-- <div id="light-warning" style="
    display: none;
    background: rgba(255, 0, 0, 0.8);
    color: white;
    padding: 10px;
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 1000;
    border-radius: 5px;
">
                Cahaya terlalu gelap, harap pindah ke lokasi yang lebih terang.
            </div> -->
        </div>

        <div class="button-container">
            <button onclick="registerFace()">Daftarkan Wajah</button>
        </div>
    </div>

    <div id="video-container" style="position: relative; display: inline-block;">
        <video id="video" data-nama="{{ $mahasiswa->nama }}" data-npm="{{ $mahasiswa->npm }}" width="640" height="480" autoplay muted></video>
    </div>
    <br>

</body>

</html>
