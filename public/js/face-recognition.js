let recognizedName = null;
let isSubmitting = false;
let unknownShown = false;
let hasAbsen = false;
let ownerName = null;

var lokasi = document.getElementById("lokasi");

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
}

function successCallback(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;

    lokasi.value = position.coords.latitude + "," + position.coords.longitude;

    var map = L.map("map").setView(
        [position.coords.latitude, position.coords.longitude],
        18,
    );

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 17,
        attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    var marker = L.marker([
        position.coords.latitude,
        position.coords.longitude,
    ]).addTo(map);

    var circle = L.circle(
        [position.coords.latitude, position.coords.longitude],
        { color: "red", fillColor: "#f03", fillOpacity: 0.5, radius: 10 },
    ).addTo(map);
}

function errorCallback(err) {
    console.log(err);
    lokasi.value = "Gagal ambil lokasi";
}

// --- Load saat halaman siap
window.addEventListener("DOMContentLoaded", async () => {
    await loadModels();
    await startVideo();
});

// --- Load model
async function loadModels() {
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
    ]);
}

// --- Start kamera
async function startVideo() {
    const video = document.getElementById("video");
    ownerName = video.getAttribute("data-nama");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
        });
        video.srcObject = stream;
    } catch (err) {
        alert("Gagal akses kamera");
        return;
    }

    video.addEventListener("loadedmetadata", async () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("video-container").appendChild(canvas);

        const displaySize = {
            width: video.videoWidth,
            height: video.videoHeight,
        };

        faceapi.matchDimensions(canvas, displaySize);

        const labeledDescriptors = await loadLabeledDescriptors();
        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);

        setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(
                detections,
                displaySize,
            );

            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            //  TIDAK ADA WAJAH (WAJAH DITUTUP / KELUAR FRAME)
            if (resizedDetections.length === 0) {
                recognizedName = null;

                if (!unknownShown && !hasAbsen) {
                    unknownShown = true;

                    Swal.fire({
                        icon: "info",
                        title: "Wajah tidak terdeteksi",
                        text: "Silakan hadapkan wajah ke kamera",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }

                return;
            }

            for (const detection of resizedDetections) {
                const bestMatch = faceMatcher.findBestMatch(
                    detection.descriptor,
                );

                const box = detection.detection.box;
                const similarity = ((1 - bestMatch.distance) * 100).toFixed(2);

                let label = "";
                let boxColor = "red";

                // WAJAH DIKENALI
                if (bestMatch.distance < 0.45) {
                    recognizedName = bestMatch.label;

                    //  VALIDASI OWNER
                    if (recognizedName !== ownerName) {
                        label = `${recognizedName} (${similarity}%)|Bukan ${ownerName}`;
                        boxColor = "red";

                        recognizedName = null;
                    } else {
                        //  OWNER VALID
                        label = `${recognizedName} (${similarity}%)`;
                        boxColor = "green";
                        unknownShown = false;

                        // 🚀 AUTO ABSEN
                        if (!isSubmitting && !hasAbsen) {
                            isSubmitting = true;

                            await sendAbsen("masuk");

                            setTimeout(() => {
                                isSubmitting = false;
                            }, 5000);
                        }
                    }
                } else {
                    label = `Tidak dikenal (${similarity}%)`;
                    boxColor = "blue";
                    recognizedName = null;
                }

                // 🎯 DRAW BOX (SELALU DIGAMBAR)
                const drawBox = new faceapi.draw.DrawBox(box, {
                    label: label,
                    boxColor: boxColor,
                });

                drawBox.draw(canvas);
            }
        }, 2000);
    });
}

// --- Kirim absensi
async function sendAbsen(tipe) {
    if (!recognizedName || hasAbsen) return;

    const video = document.getElementById("video");
    const krs = video.getAttribute("data-krs");

    try {
        const response = await fetch("/internal/absensi", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({
                krs: krs,
                nama: recognizedName,
                tipe: tipe,
            }),
        });

        const result = await response.json();

        if (result.status === "success") {
            hasAbsen = true;

            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Berhasil",
                text: "Absen disimpan",
                timer: 4000,
                showConfirmButton: false,
            });

            setTimeout(() => {
                window.location.href = "/internal/krs";
            }, 4000);
        }
    } catch (err) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Absensi gagal",
            timer: 3000,
            showConfirmButton: false,
        });
    }
}

// --- Load descriptor
async function loadLabeledDescriptors() {
    const res = await fetch("/internal/descriptors");
    const data = await res.json();

    const labeledDescriptors = [];

    data.forEach((user) => {
        if (!user.descriptor || user.descriptor.length !== 128) return;

        labeledDescriptors.push(
            new faceapi.LabeledFaceDescriptors(user.name, [
                new Float32Array(user.descriptor),
            ]),
        );
    });

    return labeledDescriptors;
}

// --- CSRF
function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
}
