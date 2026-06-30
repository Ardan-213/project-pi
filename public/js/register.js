let canvasAdded = false;

window.addEventListener("DOMContentLoaded", async () => {
    await loadModels();
    startVideo();
});

async function loadModels() {
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
    ]);
}

function getVideoBrightness(video) {
    if (video.videoWidth === 0 || video.videoHeight === 0) return 255;

    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = video.videoWidth;
    tempCanvas.height = video.videoHeight;

    const ctx = tempCanvas.getContext("2d");
    ctx.drawImage(video, 0, 0, tempCanvas.width, tempCanvas.height);

    const frame = ctx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
    const data = frame.data;

    let total = 0;
    for (let i = 0; i < data.length; i += 4) {
        const r = data[i], g = data[i + 1], b = data[i + 2];
        total += (r + g + b) / 3;
    }

    return total / (data.length / 4);
}

async function startVideo() {
    const video = document.getElementById("video");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    } catch (err) {
        console.error("Tidak bisa mengakses kamera:", err);
        alert("Gagal mengakses kamera.");
        return;
    }

    let canvas = null;
    canvasAdded = false;

    video.addEventListener("loadedmetadata", async () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };

        await new Promise((resolve) => setTimeout(resolve, 500));

        setInterval(async () => {
            if (!canvasAdded) {
                canvas = faceapi.createCanvasFromMedia(video);
                document.getElementById("video-container").appendChild(canvas);
                faceapi.matchDimensions(canvas, displaySize);
                canvasAdded = true;
            }

            const detections = await faceapi
                .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);

            const context = canvas.getContext("2d");
            context.clearRect(0, 0, canvas.width, canvas.height);

            faceapi.draw.drawDetections(canvas, resizedDetections);
        }, 500);
    });
}

async function registerFace() {
    const video = document.getElementById("video");

    const detection = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        alert("Wajah tidak terdeteksi. Silakan hadapkan wajah ke kamera dengan jelas.");
        return;
    }

    const descriptor = Array.from(detection.descriptor);
    const name = video?.getAttribute("data-nama");
    const npm = video?.getAttribute("data-npm");

    try {
        const response = await fetch("/internal/simpanDaftarWajah", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ name, npm, descriptor }),
        });

        const result = await response.json();
        if (result.status === "success") {
            alert("Wajah berhasil didaftarkan.");
        } else {
            alert("Gagal mendaftarkan wajah.");
        }
    } catch (error) {
        console.error("Error saat mengirim data:", error);
        alert("Terjadi kesalahan saat mengirim data.");
    }
}

function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "{{ csrf_token() }}";
}
