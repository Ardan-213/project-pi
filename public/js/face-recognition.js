let recognizedName = null;

// --- Load model saat halaman dimuat
window.addEventListener("DOMContentLoaded", async () => {
    await loadModels();
    await startVideo();
    setupAbsenButtons();
});

// --- Load model face-api.js
async function loadModels() {
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
    ]);
    console.log("✅ Model face-api.js sudah dimuat.");
}

// --- Start kamera + face detection
async function startVideo() {
    const video = document.getElementById("video");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
        });
        video.srcObject = stream;
    } catch (err) {
        console.error("❌ Tidak bisa akses kamera:", err);
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

        let detectedNames = new Set();
        let unknownShown = false;

        setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(
                detections,
                displaySize,
            );

            canvas
                .getContext("2d")
                .clearRect(0, 0, canvas.width, canvas.height);

            // ❗ Jika tidak ada wajah
            if (resizedDetections.length === 0) {
                recognizedName = null;
                document.getElementById("absenMasuk").disabled = true;
                document.getElementById("absenPulang").disabled = true;
                return;
            }

            resizedDetections.forEach((detection) => {
                const bestMatch = faceMatcher.findBestMatch(
                    detection.descriptor,
                );

                const box = detection.detection.box;
                const similarity = ((1 - bestMatch.distance) * 100).toFixed(2);

                let label;

                // ✅ WAJAH DIKENALI
                if (bestMatch.distance < 0.45) {
                    label = `${bestMatch.label} (${similarity}%)`;

                    recognizedName = bestMatch.label;

                    // enable tombol
                    document.getElementById("absenMasuk").disabled = false;
                    document.getElementById("absenPulang").disabled = false;

                    if (!detectedNames.has(bestMatch.label)) {
                        detectedNames.add(bestMatch.label);
                        console.log("✅ Wajah dikenali:", recognizedName);
                    }

                    unknownShown = false;
                } else {
                    label = `Wajah tidak dikenal (${similarity}%)`;

                    // ❗ JANGAN overwrite recognizedName kalau sudah ada
                    if (!recognizedName) {
                        recognizedName = null;
                    }

                    if (!unknownShown) {
                        console.log("❌ Wajah tidak dikenali");
                        unknownShown = true;
                    }

                    // disable tombol
                    document.getElementById("absenMasuk").disabled = true;
                    document.getElementById("absenPulang").disabled = true;
                }

                const drawBox = new faceapi.draw.DrawBox(box, { label });
                drawBox.draw(canvas);
            });
        }, 500);
    });
}

// --- Kirim absensi
async function sendAbsen(tipe) {
    console.log("📤 Klik absen, recognizedName:", recognizedName);

    if (!recognizedName) {
        Swal.fire({
            icon: "error",
            title: "Wajah belum dikenali",
            text: "Silakan hadapkan wajah ke kamera.",
        });
        return;
    }

    const video = document.getElementById("video");
    const krs = video ? video.getAttribute("data-krs") : null;

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
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: result.message || "Absen berhasil",
                timer: 2000,
                showConfirmButton: false,
            });

            // reset setelah sukses
            recognizedName = null;

            window.location = "/internal/krs";
        } else {
            throw new Error("Gagal absensi");
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Absensi gagal",
            text: "Terjadi kesalahan saat mengirim data.",
        });
    }
}

// --- Setup tombol
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

// --- Load descriptor dari backend
async function loadLabeledDescriptors() {
    const res = await fetch("/internal/descriptors");
    const data = await res.json();

    const labeledDescriptors = [];

    data.forEach((user) => {
        try {
            if (!user.descriptor) return;
            if (!Array.isArray(user.descriptor)) return;
            if (user.descriptor.length !== 128) return;

            labeledDescriptors.push(
                new faceapi.LabeledFaceDescriptors(user.name, [
                    new Float32Array(user.descriptor),
                ]),
            );
        } catch (e) {
            console.error("❌ Error parsing descriptor:", user.name, e);
        }
    });

    return labeledDescriptors;
}

// --- Ambil CSRF
function getCsrfToken() {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || ""
    );
}
