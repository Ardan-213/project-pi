# Flowchart Sistem Project-PI

## 1. Login & Auth

```mermaid
flowchart TD
    subgraph User[User - Mahasiswa/Admin]
        A1[Buka halaman login] --> A2[Input email & password]
        A3[Terima dashboard]
    end

    subgraph Browser[Browser / Frontend]
        B1[Tampilkan form login] --> B2[POST /login]
        B3[Redirect ke /internal/dashboard]
    end

    subgraph Server[Server - Laravel]
        C1[Validasi kredensial] --> C2{Kredensial valid?}
        C2 -- Ya --> C3[Set session auth & redirect]
        C2 -- Tidak --> C4[Return error invalid credentials]
    end

    subgraph DB[Database]
        D1[(users table)]
    end

    A1 --> B1
    A2 --> B2
    B2 --> C1
    C1 -.-> D1
    D1 -.-> C1
    C3 --> B3
    B3 --> A3
    C4 -.-> B2
```

## 2. Admin CRUD Master Data

```mermaid
flowchart TD
    subgraph User[User - Admin]
        A1[Pilih menu master data] --> A2[Isi form tambah/edit data]
        A3[Lihat hasil di DataTable]
    end

    subgraph Browser[Browser / Frontend]
        B1[Tampilkan halaman + DataTable AJAX]
        B2[POST /simpan] --> B3[Refresh DataTable]
    end

    subgraph Server[Server - Laravel]
        C1[Validasi input & simpan data] --> C2[Return JSON success/error]
    end

    subgraph DB[Database]
        D1[(fakultas / jurusan / dosen / mata_kuliah / mahasiswa)]
    end

    A1 --> B1
    A2 --> B2
    C1 -.-> D1
    D1 -.-> C1
    C2 --> B3
    B3 --> A3
```

## 3. Pendaftaran Wajah (Face Registration)

```mermaid
flowchart TD
    subgraph User[User - Mahasiswa]
        A1[Buka halaman daftar wajah] --> A2[Hadapkan wajah ke kamera] --> A3[Konfirmasi simpan wajah]
    end

    subgraph Browser[Browser / face-api.js]
        B1[Akses kamera getUserMedia] --> B2[Load model face-api.js]
        B2 --> B3[Deteksi wajah & ekstrak descriptor]
        B3 --> B4[POST /simpanDaftarWajah]
    end

    subgraph Server[Server - Laravel]
        C1[JSON encode & simpan ke mahasiswa.face_descriptor]
    end

    subgraph DB[Database]
        D1[(mahasiswa table - face_descriptor)]
    end

    A1 --> B1
    A2 --> B3
    A3 --> B4
    B4 --> C1
    C1 -.-> D1
    D1 -.-> C1
    C1 --> B4
```

## 4. KRS (Course Registration)

```mermaid
flowchart TD
    subgraph User[User - Mahasiswa]
        A1[Buka halaman KRS] --> A2[Klik Ambil pada MK tersedia]
        A3[Lihat KRS terbaru]
    end

    subgraph Browser[Browser / Frontend]
        B1[Load DataTable KRS + daftar MK] --> B2[POST /krs/input-krs]
        B3[Tampilkan hasil & refresh table]
    end

    subgraph Server[Server - Laravel]
        C1[tentukanSemester berdasarkan bulan] --> C2{Cek kuota kuota_orang &gt; 0?}
        C2 -- Ya --> C3[Kurangi kuota & simpan KRS]
        C2 -- Tidak --> C4[Return error Kuota penuh]
    end

    subgraph DB[Database]
        D1[(mata_kuliah - kuota_orang)]
        D2[(krs)]
    end

    A1 --> B1
    A2 --> B2
    B2 --> C1
    C1 --> C2
    C2 -.-> D1
    D1 -.-> C2
    C3 --> D2
    C3 -.-> D1
    C3 --> B3
    C4 --> B2
    B3 --> A3
```

## 5. Absen Masuk

```mermaid
flowchart TD
    subgraph User[User - Mahasiswa]
        M1[Klik Absen Masuk pada KRS] --> M2[Berdiri di lokasi kelas]
        M3[Kedip 3x untuk verifikasi liveness]
    end

    subgraph Browser[Browser / face-api.js]
        F1[Buka halaman absensi masuk] --> F2[Dapatkan lokasi geolocation API]
        F2 --> F3[Load face-api.js + /descriptors]
        F3 --> F4[Deteksi wajah & FaceMatcher match]
        F4 --> F5[Hitung EAR Eye Aspect Ratio]
        F5 --> F6{Still frame? anti-spoofing}
        F6 -- Gerak --> F7[Update blink UI & timeout reset]
        F6 -- Foto diam --> F4
        F7 --> F8[POST /absen_masuk krs, lat, lng]
    end

    subgraph Server[Server - Laravel]
        S1{Cek radius Haversine &lt; 50m?}
        S2{Cek waktu sekarang &gt;= waktu_mulai?}
        S3[Insert riwayat_absensi krs_id, absensi_masuk]
        S4[Return error di luar radius]
        S5[Return error belum mulai]
    end

    subgraph DB[Database]
        D1[(riwayat_absensi)]
    end

    M2 --> F4
    M3 --> F7
    F8 --> S1
    S1 -- Dalam radius --> S2
    S1 -- Luar radius --> S4
    S2 -- Sudah mulai --> S3
    S2 -- Belum mulai --> S5
    S3 -.-> D1
    D1 -.-> S3
    S3 --> F8
    S4 --> F8
    S5 --> F8
```

## 6. Absen Pulang

```mermaid
flowchart TD
    subgraph User[User - Mahasiswa]
        P1[Klik Absen Pulang] --> P2[Kedip 3x verifikasi]
    end

    subgraph Browser[Browser / face-api.js]
        Q1[Buka halaman absensi pulang] --> Q2[Face match + blink detection]
        Q2 --> Q3[POST /absen_pulang krs, lat, lng]
    end

    subgraph Server[Server - Laravel]
        R1{Cek radius Haversine &lt; 50m?}
        R2{Cek waktu sekarang &gt;= waktu_selesai?}
        R3{Cek sudah absen_keluar sebelumnya?}
        R4[Update riwayat_absensi absensi_keluar = now]
        R5[Return error di luar radius]
        R6[Return error belum waktunya]
        R7[Return error sudah absen pulang]
    end

    subgraph DB[Database]
        S1[(riwayat_absensi)]
    end

    Q3 --> R1
    R1 -- Dalam radius --> R2
    R1 -- Luar radius --> R5
    R2 -- Sudah waktunya --> R3
    R2 -- Belum selesai --> R6
    R3 -.-> S1
    S1 -.-> R3
    R3 -- Belum pulang --> R4
    R3 -- Sudah pulang --> R7
    R4 -.-> S1
    S1 -.-> R4
    R4 --> Q3
    R5 --> Q3
    R6 --> Q3
    R7 --> Q3
```
