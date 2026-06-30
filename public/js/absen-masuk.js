let recognizedName = null;
let isSubmitting = false;
let unknownShown = false;
let hasAbsen = false;
let ownerName = null;
let currentLat = null;
let currentLng = null;
let blinkCount = 0;
let isEyeClosed = false;
let isLockingGreen = false; // Untuk menahan kotak hijau agar tidak langsung hilang/berubah

var lokasi = document.getElementById("lokasi");

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
}

function successCallback(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;

    currentLat = lat;
    currentLng = lng;

    lokasi.value = position.coords.latitude + "," + position.coords.longitude;

    var map = L.map("map").setView(
        [position.coords.latitude, position.coords.longitude],
        13,
    );

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    var marker = L.marker([
        position.coords.latitude,
        position.coords.longitude,
    ]).addTo(map);

    // ini buletan radius pasca sarjana

    // kedua
    var circle = L.circle(
        [
            // -5.375329714761104,
            // 105.24604359669844
            position.coords.latitude,
            position.coords.longitude,
        ],
        {
            color: "red",
            fillColor: "#f03",
            fillOpacity: 0.5,
            radius: 100,
        },
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

function getEAR(eyeLandmarks) {
    // Jarak vertikal
    const p2_p6 = Math.hypot(
        eyeLandmarks[1].x - eyeLandmarks[5].x,
        eyeLandmarks[1].y - eyeLandmarks[5].y,
    );
    const p3_p5 = Math.hypot(
        eyeLandmarks[2].x - eyeLandmarks[4].x,
        eyeLandmarks[2].y - eyeLandmarks[4].y,
    );
    // Jarak horizontal
    const p1_p4 = Math.hypot(
        eyeLandmarks[0].x - eyeLandmarks[3].x,
        eyeLandmarks[0].y - eyeLandmarks[3].y,
    );

    return (p2_p6 + p3_p5) / (2.0 * p1_p4);
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

        if (!labeledDescriptors.length) {
            Swal.fire({
                icon: "error",
                title: "Data wajah kosong",
                text: "Silakan daftar wajah terlebih dahulu",
            });
            return;
        }

        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);

        function getEAR(eye) {
            const p2_p6 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
            const p3_p5 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
            const p1_p4 = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
            return (p2_p6 + p3_p5) / (2.0 * p1_p4);
        }

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

            if (resizedDetections.length === 0) {
                recognizedName = null;
                // Jika tidak ada wajah, matikan lock agar sistem reset
                if (!isLockingGreen) {
                    blinkCount = 0;
                    isEyeClosed = false;
                }

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

                const landmarks = detection.landmarks;
                const leftEye = landmarks.getLeftEye();
                const rightEye = landmarks.getRightEye();

                const leftEAR = getEAR(leftEye);
                const rightEAR = getEAR(rightEye);
                const avgEAR = (leftEAR + rightEAR) / 2;

                // Hanya hitung kedipan jika sedang tidak dalam mode "mengunci kotak hijau"
                if (!isLockingGreen) {
                    if (avgEAR < 0.25) {
                        isEyeClosed = true;
                    } else {
                        if (isEyeClosed) {
                            blinkCount++;
                            isEyeClosed = false;
                        }
                    }
                }

                if (bestMatch.distance < 0.45) {
                    recognizedName = bestMatch.label;

                    if (recognizedName !== ownerName) {
                        label = `${recognizedName} (${similarity}%)|Bukan ${ownerName}`;
                        boxColor = "red";
                        recognizedName = null;
                        blinkCount = 0;
                    } else {
                        unknownShown = false;

                        // 🟢 JIKA SUDAH BERKEDIP ATAU SEDANG DIKUNCI HIJAU
                        if (blinkCount >= 1 || isLockingGreen) {
                            label = `${recognizedName} (${similarity}%) - Kedip OK!`;
                            boxColor = "green";

                            if (!isSubmitting && !hasAbsen) {
                                isSubmitting = true;
                                isLockingGreen = true; // 🔒 Kunci tampilan warna hijau

                                // Berikan jeda 1.5 detik (1500ms) agar user melihat kotak hijau dulu baru absen ditembak
                                setTimeout(async () => {
                                    await sendAbsen("masuk");
                                    isSubmitting = false;
                                    isLockingGreen = false; // 🔓 Buka kunci setelah proses selesai
                                }, 1500);
                            }
                        } else {
                            label = `${ownerName} (${similarity}%) | Silakan BERKEDIP!`;
                            boxColor = "orange";
                        }
                    }
                } else {
                    label = `Tidak dikenal (${similarity}%)`;
                    boxColor = "blue";
                    recognizedName = null;
                    if (!isLockingGreen) blinkCount = 0;
                }

                const drawBox = new faceapi.draw.DrawBox(box, {
                    label: label,
                    boxColor: boxColor,
                });
                drawBox.draw(canvas);
            }
        }, 200);
    });
}

// --- Kirim absensi
async function sendAbsen(tipe) {
    if (!recognizedName || hasAbsen) return;

    const video = document.getElementById("video");
    const krs = video.getAttribute("data-krs");

    try {
        const response = await fetch("/internal/absen_masuk", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({
                krs: krs,
                nama: recognizedName,
                tipe: tipe,
                currentLatUser: currentLat,
                currentLngUser: currentLng,
            }),
        });

        const result = await response.json();

        if (result.status === "success") {
            hasAbsen = true;

            blinkCount = 0;
            isEyeClosed = false;
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

        if (result.status === "error terlambat") {
            Swal.fire({
                icon: "error",
                title: "Maaf",
                text: result.message,
                timer: 3000,
                showConfirmButton: true,
            });
        }
        if (result.status === "error belum mulai") {
            Swal.fire({
                icon: "error",
                title: "Maaf",
                text: result.message,
                timer: 3000,
                showConfirmButton: true,
            });
        }

        if (result.status === "error radius") {
            Swal.fire({
                icon: "error",
                title: "Maaf",
                text: result.message,
                timer: 2000,
                showConfirmButton: true,
            });
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
