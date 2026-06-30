// === STATE ===
let recognizedName = null;
let isSubmitting = false;
let unknownShown = false;
let hasAbsen = false;
let ownerName = null;
let currentLat = null;
let currentLng = null;
let blinkCount = 0;
let isEyeClosed = false;
let blinkStartTime = null;
let lastLandmarks = null;
let stillFrameCount = 0;

const BLINK_THRESHOLD = 0.27;
const REQUIRED_BLINKS = 3;
const BLINK_TIMEOUT = 10000;
const STILL_FRAME_LIMIT = 15;

const lokasi = document.getElementById("lokasi");

// === GEOLOCATION ===
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
}

function successCallback(position) {
    currentLat = position.coords.latitude;
    currentLng = position.coords.longitude;
    lokasi.value = currentLat + "," + currentLng;

    const map = L.map("map").setView([currentLat, currentLng], 15);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    L.marker([currentLat, currentLng]).addTo(map);
    L.circle([-5.375329714761104, 105.24604359669844], {
        color: "red",
        fillColor: "#f03",
        fillOpacity: 0.5,
        radius: 50,
    }).addTo(map);
}

function errorCallback(err) {
    console.log(err);
    lokasi.value = "Gagal ambil lokasi";
}

// === LOAD ===
window.addEventListener("DOMContentLoaded", async () => {
    await loadModels();
    await startVideo();
    createBlinkUI();
});

async function loadModels() {
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
    ]);
}

// === EAR (Eye Aspect Ratio) ===
function getEAR(eye) {
    const p2_p6 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
    const p3_p5 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
    const p1_p4 = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
    return (p2_p6 + p3_p5) / (2.0 * p1_p4);
}

// === MICRO-MOVEMENT ANTI-SPOOFING ===
function detectStillFrame(landmarks) {
    if (!lastLandmarks) {
        lastLandmarks = landmarks;
        return false;
    }
    let totalMovement = 0;
    const count = Math.min(landmarks.length, lastLandmarks.length);
    for (let i = 0; i < count; i++) {
        totalMovement += Math.hypot(
            landmarks[i].x - lastLandmarks[i].x,
            landmarks[i].y - lastLandmarks[i].y
        );
    }
    const avgMovement = totalMovement / count;
    lastLandmarks = landmarks;

    if (avgMovement < 0.3) {
        stillFrameCount++;
    } else {
        stillFrameCount = 0;
    }
    return stillFrameCount >= STILL_FRAME_LIMIT;
}

// === BLINK PROGRESS UI ===
function createBlinkUI() {
    const container = document.getElementById("video-container");
    const div = document.createElement("div");
    div.id = "blink-progress";
    div.style.cssText = `
        text-align:center; margin-top:8px; font-size:18px; font-family:sans-serif;
        min-height:28px; transition: opacity 0.3s ease;
    `;
    div.innerHTML = `
        <span id="blink-dots">
            <span class="bdot" data-i="0" style="color:#ccc;font-size:24px;transition:color 0.3s">●</span>
            <span class="bdot" data-i="1" style="color:#ccc;font-size:24px;transition:color 0.3s">●</span>
            <span class="bdot" data-i="2" style="color:#ccc;font-size:24px;transition:color 0.3s">●</span>
        </span>
        <span id="blink-label" style="color:#888;margin-left:8px;font-size:14px">Menunggu wajah...</span>
    `;
    container.appendChild(div);
}

function updateBlinkUI(count, label, color) {
    const dots = document.querySelectorAll(".bdot");
    dots.forEach((dot, i) => {
        dot.style.color = i < count ? "#22c55e" : "#ccc";
    });
    const lbl = document.getElementById("blink-label");
    if (lbl) {
        lbl.textContent = label;
        lbl.style.color = color;
    }
}

function resetBlinkUI() {
    blinkCount = 0;
    blinkStartTime = null;
    updateBlinkUI(0, "Menunggu wajah...", "#888");
}

// === KAMERA ===
async function startVideo() {
    const video = document.getElementById("video");
    ownerName = video.getAttribute("data-nama");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    } catch (err) {
        alert("Gagal akses kamera");
        return;
    }

    video.addEventListener("loadedmetadata", async () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("video-container").appendChild(canvas);

        const displaySize = { width: video.videoWidth, height: video.videoHeight };
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

        setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // === NO FACE ===
            if (resizedDetections.length === 0) {
                recognizedName = null;
                if (!hasAbsen) resetBlinkUI();

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
                const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                const box = detection.detection.box;
                const similarity = ((1 - bestMatch.distance) * 100).toFixed(2);

                let label = "";
                let boxColor = "red";

                const landmarks = detection.landmarks;
                const leftEye = landmarks.getLeftEye();
                const rightEye = landmarks.getRightEye();
                const avgEAR = (getEAR(leftEye) + getEAR(rightEye)) / 2;
                console.log('EAR:', avgEAR.toFixed(3), '| BLINK_THRESHOLD:', BLINK_THRESHOLD);

                // Check if face is a still photo (anti-spoofing)
                const allPoints = landmarks.positions;
                const isStill = detectStillFrame(allPoints);

                if (bestMatch.distance >= 0.45) {
                    label = `Tidak dikenal (${similarity}%)`;
                    boxColor = "blue";
                    recognizedName = null;
                    if (!hasAbsen) resetBlinkUI();
                } else if (bestMatch.label !== ownerName) {
                    label = `${bestMatch.label} (${similarity}%) | Bukan ${ownerName}`;
                    boxColor = "red";
                    recognizedName = null;
                    if (!hasAbsen) resetBlinkUI();
                } else {
                    recognizedName = ownerName;
                    unknownShown = false;

                    // Anti-spoofing: foto diam
                    if (isStill) {
                        label = `${ownerName} (${similarity}%) | Foto? Gerakkan kepala`;
                        boxColor = "purple";
                        if (!hasAbsen) resetBlinkUI();

                    // BLINK DETECTION
                    } else if (!hasAbsen) {
                        if (!blinkStartTime) blinkStartTime = Date.now();

                        // Timeout reset
                        if (Date.now() - blinkStartTime > BLINK_TIMEOUT) {
                            resetBlinkUI();
                            blinkStartTime = Date.now();
                        }

                        if (avgEAR < BLINK_THRESHOLD) {
                            isEyeClosed = true;
                        } else if (isEyeClosed) {
                            blinkCount++;
                            isEyeClosed = false;
                            blinkStartTime = Date.now();
                        }

                        const progress = Math.min(blinkCount, REQUIRED_BLINKS);
                        updateBlinkUI(progress, `Kedip ${progress}/${REQUIRED_BLINKS}`, "#22c55e");

                        if (blinkCount >= REQUIRED_BLINKS) {
                            label = `${ownerName} (${similarity}%) | ✅ Wajah terverifikasi!`;
                            boxColor = "green";

                            if (!isSubmitting) {
                                isSubmitting = true;
                                updateBlinkUI(REQUIRED_BLINKS, "✅ Verifikasi berhasil! Mengirim...", "#22c55e");

                                setTimeout(async () => {
                                    await sendAbsen("masuk");
                                    isSubmitting = false;
                                }, 2000);
                            }
                        } else {
                            label = `${ownerName} (${similarity}%) | Kedip ${blinkCount}/${REQUIRED_BLINKS}`;
                            boxColor = blinkCount > 0 ? "#f59e0b" : "#f97316";
                        }
                    } else {
                        label = `${ownerName} (${similarity}%) | ✅ Sudah absen`;
                        boxColor = "green";
                    }
                }

                const drawBox = new faceapi.draw.DrawBox(box, {
                    label, boxColor,
                });
                drawBox.draw(canvas);
            }
        }, 200);
    });
}

// === KIRIM ABSENSI ===
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
                krs,
                nama: recognizedName,
                tipe,
                currentLatUser: currentLat,
                currentLngUser: currentLng,
            }),
        });

        const result = await response.json();

        if (result.status === "success") {
            hasAbsen = true;
            updateBlinkUI(REQUIRED_BLINKS, "✅ Absen berhasil! ✅", "#22c55e");

            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Berhasil",
                text: "Absen disimpan",
                timer: 6000,
                showConfirmButton: false,
            });

            let countdown = 5;
            updateBlinkUI(REQUIRED_BLINKS, `Redirect dalam ${countdown}...`, "#22c55e");
            const interval = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    updateBlinkUI(REQUIRED_BLINKS, `Redirect dalam ${countdown}...`, "#22c55e");
                } else {
                    clearInterval(interval);
                    window.location.href = "/internal/krs";
                }
            }, 1000);
        }

        if (result.status === "error radius") {
            Swal.fire({
                icon: "error",
                title: "Maaf",
                text: result.message,
                timer: 3000,
                showConfirmButton: true,
            });
            isSubmitting = false;
        }
        if (result.status === "error belum mulai") {
            Swal.fire({
                icon: "error",
                title: "Maaf",
                text: result.message,
                timer: 3000,
                showConfirmButton: true,
            });
            isSubmitting = false;
        }
    } catch (err) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Absensi gagal",
            text: err.message || "Coba lagi",
            timer: 3000,
            showConfirmButton: false,
        });
        isSubmitting = false;
    }
}

// === LOAD DESCRIPTOR ===
async function loadLabeledDescriptors() {
    const res = await fetch("/internal/descriptors");
    const data = await res.json();
    const labeledDescriptors = [];

    data.forEach((user) => {
        if (!user.descriptor || user.descriptor.length !== 128) return;
        labeledDescriptors.push(
            new faceapi.LabeledFaceDescriptors(user.name, [new Float32Array(user.descriptor)])
        );
    });

    return labeledDescriptors;
}

// === CSRF ===
function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
}
