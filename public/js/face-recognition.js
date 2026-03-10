let recognizedName = "";

// --- Load model saat halaman dimuat
window.addEventListener("DOMContentLoaded", async () => {
    await loadModels();
    await startVideo();
    setupAbsenButtons(); // setup listener hanya sekali
});

// --- Fungsi untuk load model face-api.js
async function loadModels() {
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
    ]);
    console.log("Model face-api.js sudah dimuat.");
}

// --- Fungsi untuk mulai video dan deteksi wajah
async function startVideo() {
    const video = document.getElementById("video");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
        });
        video.srcObject = stream;
    } catch (err) {
        console.error("Tidak bisa mengakses kamera:", err);
        alert("Gagal mengakses kamera.");
        return;
    }

    video.addEventListener("loadedmetadata", async () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        canvas.setAttribute("id", "overlay");
        document.getElementById("video-container").appendChild(canvas);

        const displaySize = {
            width: video.videoWidth,
            height: video.videoHeight,
        };
        faceapi.matchDimensions(canvas, displaySize);

        const labeledDescriptors = await loadLabeledDescriptors();
        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);

        const sent = [];

        setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(
                detections,
                displaySize
            );
            canvas
                .getContext("2d")
                .clearRect(0, 0, canvas.width, canvas.height);

            resizedDetections.forEach((detection) => {
                const bestMatch = faceMatcher.findBestMatch(
                    detection.descriptor
                );
                const box = detection.detection.box;

                let label;
                let similarity = ((1 - bestMatch.distance) * 100).toFixed(2); // konversi ke persentase

                if (bestMatch.distance < 0.45) {
                    label = `${bestMatch.label} (${similarity}%)`;
                } else {
                    label = `Wajah tidak dikenal (${similarity}%)`;
                }

                const drawBox = new faceapi.draw.DrawBox(box, { label });
                drawBox.draw(canvas);

                let sent = [];
                let unknownShown = false; // flag untuk menghindari log berulang jika wajah tidak dikenali

                if (label !== "Wajah tidak dikenal" && !sent.includes(label)) {
                    sent.push(label);
                    recognizedName = bestMatch.label; // hanya nama, tanpa similarity

                    // Aktifkan tombol absen
                    document.getElementById("absenMasuk").disabled = false;
                    document.getElementById("absenPulang").disabled = false;

                    console.log("Wajah dikenali sebagai:", recognizedName);

                    // Reset flag unknown karena sudah ada yang dikenali
                    unknownShown = false;
                } else if (!unknownShown) {
                    recognizedName = "Tidak diketahui"; // Atur nilai default
                    console.log(
                        "Wajah tidak dikenali. recognizedName:",
                        recognizedName
                    );
                    unknownShown = true; // supaya tidak muncul lagi di log
                }
            });
        }, 500); // setiap 0.5 detik
    });
}

// --- Fungsi kirim absen
async function sendAbsen(tipe) {
    if (!recognizedName) return;

    const krs = video ? video.getAttribute("data-krs") : null;
    console.log(krs);

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
                tipe: tipe, // 'masuk' atau 'pulang'
            }),
        });

        const result = await response.json();

        if (result.status === "success") {
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: result.message || "Absen berhasil",
                timer: 2000,
                showConfirmButton: false,
            });
        }

        recognizedName = null;
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Wajah tidak dikenali",
            text: "Absensi gagal dilakukan!",
        });
    }
}

// --- Fungsi hanya pasang listener sekali
function setupAbsenButtons() {
    document
        .getElementById("absenMasuk")
        .addEventListener("click", async () => {
            await sendAbsen("masuk");
        });

    document
        .getElementById("absenPulang")
        .addEventListener("click", async () => {
            await sendAbsen("pulang");
        });
}

// --- Ambil deskriptor wajah dari backend Laravel
async function loadLabeledDescriptors() {
    const res = await fetch("/internal/descriptors");
    const data = await res.json();

    return Promise.all(
        data.map(
            (user) =>
                new faceapi.LabeledFaceDescriptors(user.name, [
                    new Float32Array(user.descriptor),
                ])
        )
    );
}

// --- Ambil CSRF token dari meta tag
function getCsrfToken() {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "{{ csrf_token() }}"
    );
}
