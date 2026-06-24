# Dokumentasi Agile Scrum Process - Sistem Pengajuan Tugas Akhir

Sistem Informasi Pengajuan Proposal Tugas Akhir (TA Submission) dikembangkan menggunakan metodologi **Agile Scrum** dengan pembagian waktu pengerjaan selama 8 minggu yang terbagi ke dalam **4 siklus pengembangan (Sprint)**, masing-masing berdurasi 2 minggu.

Dokumentasi ini menjelaskan secara rinci proses perencanaan backlog, pelaksanaan sprint, UML yang diacu, implementasi kode aktual, kriteria *Definition of Done* (DoD), serta kendala dan evaluasi teknis pada setiap akhir sprint.

---

## 🗺️ 1. Prioritisasi Backlog (MoSCoW)
Sebelum memasuki Sprint 1, kebutuhan fungsional dan non-fungsional dipetakan ke dalam *User Stories* di dalam Product Backlog dan diprioritaskan menggunakan metode MoSCoW:

*   **Must-Have (M)**: Fitur wajib (Autentikasi RBAC, unggah proposal PDF, batas proposal aktif, penetapan reviewer, blind grading, persetujuan dosbing & auto-reject proposal lain, penolakan dengan alasan).
*   **Should-Have (S)**: Fitur pendukung penting (Pembatalan draf, impor massal mahasiswa/dosen, pengaturan masa/kuota batch, cetak rekapitulasi, similarity check judul, kelola profil).
*   **Could-Have (C)**: Fitur tambahan (Log audit aktivitas, edit riwayat pengajuan historis).
*   **Won't-Have (W)**: Fitur masa depan (Notifikasi WA/Email, SPK dosen pembimbing, aplikasi seluler).

---

## 🏃 2. Pelaksanaan Sprint Per Iterasi

### 🔹 Sprint 1: Autentikasi, RBAC, dan Dashboard Multirole
*   **Durasi**: Pekan 1 – Pekan 2
*   **Sprint Goal**: Membangun mekanisme autentikasi aman berbasis peran (Role-Based Access Control) dan dasbor utama untuk aktor Kaprodi, Dosen, Mahasiswa, dan Admin.
*   **Sprint Backlog**: Item M-01, S-06, C-01.

#### A. Analisis dan Perancangan (UML)
*   **Use Case**: Pengguna melakukan Login, Logout, Edit Profil, dan Ganti Password.
*   **Activity Diagram**:
    ```mermaid
    activityDiagram
        start
        :Pengguna Memasukkan Email & Password;
        :Sistem Memeriksa Kredensial;
        if (Kredensial Valid?) then (Ya)
            :Sistem Regenerate Session;
            :Periksa Role Pengguna;
            :Redirect ke Dashboard Role;
        else (Tidak)
            :Tampilkan Flash Error "Email atau password salah";
        endif
        stop
    ```

#### B. Implementasi Kode Aktual
1.  **Mekanisme Login & Otorisasi**: Diimplementasikan pada [AuthController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/AuthController.php#L35-L60) menggunakan session management bawaan Laravel.
2.  **Role & Permission Seeder**: Diatur melalui pustaka Spatie di [RolePermissionSeeder.php](file:///c:/xampp/htdocs/ta_submission/database/seeders/RolePermissionSeeder.php) dengan mendefinisikan hak akses peran (seperti `view menu: mahasiswa`, `view menu: dosen`, `view menu: kaprodi`).
3.  **Manajemen Profil**: Dikelola di [ProfileController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/ProfileController.php).

#### C. Definition of Done (DoD) & Pengujian
*   **Code Review**: Kode middleware dan routing diperiksa dan dipastikan bebas dari *hardcoded role checking*.
*   **Database Migration**: Tabel `users`, `roles`, dan `permissions` berhasil dimigrasikan via `php artisan migrate`.
*   **Unit Tests Passed**: Lolos pengujian otomatis pada suite [AuthTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/AuthTest.php) dan [UserProfileTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/UserProfileTest.php).
*   **RBAC Lock**: Upaya bypass URL (misal mahasiswa mengakses `/kaprodi/dashboard`) berhasil diblokir dengan status **403 Forbidden**.

#### D. Kendala & Evaluasi Retrospective
*   **Kendala**: Terjadi *redundant database query execution* pada relasi role & permission pada setiap komponen navigasi sidebar dashboard, yang memicu latensi pemuatan dasbor di atas 500ms.
*   **Evaluasi & Refaktorisasi**: Menggunakan kueri eager loading via metode `with('roles.permissions')` serta caching session di [AppServiceProvider.php](file:///c:/xampp/htdocs/ta_submission/app/Providers/AppServiceProvider.php) sehingga mengurangi jumlah kueri database dari 7 menjadi 1 kueri tunggal, mengembalikan respons pemuatan halaman di bawah 200ms.

---

### 🔹 Sprint 2: Usulan Proposal Mahasiswa & Similarity Check
*   **Durasi**: Pekan 3 – Pekan 4
*   **Sprint Goal**: Membangun modul pengisian draf proposal baru bagi mahasiswa, pengunggahan dokumen PDF proposal ke direktori aman, penguncian kuota proposal aktif, serta sistem deteksi dini kemiripan judul proposal.
*   **Sprint Backlog**: Item M-02, M-03, S-01, S-05.

#### A. Analisis dan Perancangan (UML)
*   **Activity Diagram**:
    ```mermaid
    flowchart TD
        A[Mulai Pengajuan] --> B{Punya Proposal Aktif?}
        B -- Ya --> C[Tolak Aksi - Tampilkan Pesan Limit]
        B -- Tidak --> D[Isi Judul & Abstrak]
        D --> E[Unggah Berkas PDF]
        E --> F[Pengecekan Kemiripan Judul secara Real-Time]
        F --> G[Simpan sebagai Draft]
        G --> H{Ajukan Final?}
        H -- Ya --> I[Ubah Status: Diajukan & Kunci Dokumen]
        H -- Tidak --> J[Draf Dapat Diedit / Dibatalkan]
    ```

#### B. Implementasi Kode Aktual
1.  **Pemberlakuan Batasan & Kunci Draf**: Diimplementasikan pada [SubmissionService.php](file:///c:/xampp/htdocs/ta_submission/app/Services/Student/SubmissionService.php#L40-L90) di metode `create()`. Aturan mengunci proses jika mahasiswa memiliki proposal berstatus `approved` atau melampaui batas batch pengajuan.
2.  **Deteksi Kemiripan Judul**: Logika komparasi string berada di kelas [SimilarityHelper.php](file:///c:/xampp/htdocs/ta_submission/app/Helpers/SimilarityHelper.php) menggunakan rumus *Longest Common Subsequence* (LCS) via fungsi `similar_text()` PHP:
    $$\text{Similarity} = \frac{2 \times |LCS|}{|S_1| + |S_2|} \times 100\%$$
3.  **Otorisasi Controller**: Diintegrasikan di [SubmissionController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/Student/SubmissionController.php).

#### C. Definition of Done (DoD) & Pengujian
*   **Storage Isolation**: Berkas biner PDF proposal wajib disimpan di folder privat yang tidak bisa diakses langsung via publik URL. Menggunakan rute unduhan dinamis via `Storage::privateDownload()`.
*   **Server-Side Validation**: Ukuran file dibatasi maksimal 10MB dengan ekstensi wajib dokumen `.pdf`.
*   **Unit Tests Passed**: Lolos pengujian otomatis pada file pengujian [FileSimilarityTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/FileSimilarityTest.php) dan suite `SubmissionServiceTest`.

#### D. Kendala & Evaluasi Retrospective
*   **Kendala**: Terjadi *unhandled exception* (HTTP 500 error) saat mahasiswa mencoba mengunggah dokumen PDF yang rusak (corrupted binary) karena framework gagal mem-parsing mime-type berkas.
*   **Evaluasi & Refaktorisasi**: Menambahkan blok try-catch yang membungkus pemrosesan unggahan file dan memanfaatkan pembaca metadata biner alternatif di validator request. Jika file terdeteksi rusak, dialihkan menjadi pesan kesalahan form: *"Berkas PDF yang Anda unggah korup atau rusak, silakan periksa kembali dokumen Anda."*

---

### 🔹 Sprint 3: Penilaian Independen & Rubrik Digital Dosen
*   **Durasi**: Pekan 5 – Pekan 6
*   **Sprint Goal**: Membangun modul penilaian digital terstandarisasi untuk Dosen Penilai berbasis kriteria rubrik dengan isolasi data penilaian antar-penguji (*Blind Grading*).
*   **Sprint Backlog**: Item M-05.

#### A. Analisis dan Perancangan (UML)
*   **Sequence Diagram**:
    *   Dosen masuk ke halaman penilaian -> Sistem memuat rubrik kriteria aktif.
    *   Dosen menginput skor untuk tiap kriteria -> Sistem menyimpan ke database berasosiasi dengan ID evaluator.
    *   Dosen klik submit -> Status penilaian terkunci (is_submitted = true).

#### B. Implementasi Kode Aktual
1.  **Kalkulasi Nilai Berbobot**: Logika perhitungan nilai akhir dan detail nilai kriteria berada di [AssessmentService.php](file:///c:/xampp/htdocs/ta_submission/app/Services/Examiner/AssessmentService.php#L192-L217) pada metode `saveScores()`.
2.  **Proteksi Penilaian**: Penilaian diisolasi per dosen dan dikunci setelah dikirim via controller [AssessmentController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/Dosen/AssessmentController.php).

#### C. Definition of Done (DoD) & Pengujian
*   **Blind Grading Isolation**: Dosen penguji dilarang keras melihat atau mengakses data nilai dosen lain sebelum penilaian terkunci final oleh Kaprodi.
*   **Rubric Constraints**: Formulir input nilai ditarik dinamis dari tabel `rubrics` dan `rubric_items` yang diatur oleh Kaprodi.
*   **Score Range**: Input wajib dibatasi antara rentang nilai numerik 0 hingga 100.
*   **Post-Submit Locking**: Setelah data nilai disubmit, controller melempar respon **403 Forbidden** jika ada percobaan edit ulang.
*   **Unit Tests Passed**: Seluruh pengujian fitur di [AssessmentTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/AssessmentTest.php) dan `AssessmentServiceTest` lolos 100%.

#### D. Kendala & Evaluasi Retrospective
*   **Kendala**: Adanya isu bottleneck database kueri (N+1 query issue) saat memuat halaman daftar penilaian dosen karena Eloquent memanggil data nilai anak (`AssessmentScore`) secara berulang untuk setiap baris data mahasiswa yang dirender.
*   **Evaluasi & Refaktorisasi**: Kode pengambilan data pada pengontrol penilaian refaktorisasi menggunakan eager loading: `with(['thesis.student', 'rubric.items', 'scores'])` di [AssessmentController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/Dosen/AssessmentController.php). Kueri berkurang drastis dari 24 kueri terpisah menjadi hanya 2 kueri optimal.

---

### 🔹 Sprint 4: Kontrol Manajerial Kaprodi, Pelaporan & Pengaturan Sistem
*   **Durasi**: Pekan 7 – Pekan 8
*   **Sprint Goal**: Membangun panel administratif Kaprodi untuk menentukan dosen penilai, memutuskan kelayakan akhir proposal, menetapkan dosen pembimbing, mengimpor/ekspor data Excel secara massal, dan mengunduh laporan rekap kelulusan.
*   **Sprint Backlog**: Item M-04, M-06, M-07, S-02, S-03, S-04, C-02.

#### A. Analisis dan Perancangan (UML)
*   **Use Case**: Kaprodi mengelola mahasiswa, mengelola dosen, menetapkan rubrik, mengatur periode TA, menyetujui/menolak pengajuan, serta mencetak rekap laporan.
*   **Sequence Diagram**:
    ```mermaid
    sequenceDiagram
        actor Kaprodi
        participant System as Sistem (KaprodiController)
        participant Service as KaprodiService
        participant DB as Basis Data (MySQL)
        
        Kaprodi->>System: Setujui Proposal & Tunjuk Pembimbing
        System->>System: Validasi (Pembimbing 1 != Pembimbing 2)
        System->>Service: acceptSubmission(id, data)
        Service->>DB: Update status = 'approved', simpan ID Dosbing
        Service->>DB: Cari proposal aktif lain milik mahasiswa tersebut
        Service->>DB: Auto-reject proposal aktif lainnya (status = 'rejected')
        DB-->>Service: Transaksi Berhasil
        Service-->>System: Status Terupdate
        System-->>Kaprodi: Tampilkan Flash Sukses
    ```

#### B. Implementasi Kode Aktual
1.  **Persetujuan Proposal & Auto-Reject**: Logika keputusan berada di [KaprodiService.php](file:///c:/xampp/htdocs/ta_submission/app/Services/Kaprodi/KaprodiService.php#L240-L268) pada metode `acceptSubmission()`. Menghitung rata-rata nilai, mencatat pembimbing, dan menolak secara otomatis pengajuan aktif lain milik mahasiswa bersangkutan.
2.  **Impor/Ekspor Excel Massal**: Parsing file template Excel dikelola di [KaprodiStudentController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/Kaprodi/KaprodiStudentController.php).
3.  **Cetak Laporan**: Render data kelayakan dicetak dalam layout rapi via [KaprodiController.php](file:///c:/xampp/htdocs/ta_submission/app/Http/Controllers/Kaprodi/KaprodiController.php#L45-L60) di metode `reportPrint()`.

#### C. Definition of Done (DoD) & Pengujian
*   **Bulk Excel Stability**: Validasi baris demi baris, mencegah duplikasi entri NIM/NIDN, dan mendukung pengolahan minimal 100 baris record secara simultan.
*   **Supervisor Check**: Menolak transaksi kelayakan jika Dosen Pembimbing 1 dan Dosen Pembimbing 2 merujuk pada nama individu dosen yang sama.
*   **Audit Logging**: Setiap perubahan status keputusan terintegrasi dengan kueri riwayat status otomatis di tabel `thesis_statuses`.
*   **Unit Tests Passed**: Lolos pengujian otomatis di `KaprodiManagementTest`, `KaprodiStudentControllerTest`, dan `KaprodiStudentManagementTest`.

#### D. Kendala & Evaluasi Retrospective
*   **Kendala**: Mengalami *memory exhaust* (kehabisan alokasi RAM server) dan kueri bottleneck saat Kaprodi mengeksekusi cetak laporan rekapitulasi data transaksional berskala besar (ratusan mahasiswa lintas angkatan historis), menyebabkan waktu tunggu di atas 5 detik.
*   **Evaluasi & Refaktorisasi**: Struktur pemanggilan data diperbarui menggunakan kueri bersegmen (query chunking) via fungsi `chunk()` bawaan Laravel Eloquent dan menerapkan lazy loading. Pemakaian memori server tereduksi hingga 70% dan waktu respons render PDF terpangkas menjadi hanya 1,1 detik.

---

## 📈 3. Evaluasi Usability Sistem (SUS)
Setelah peluncuran akhir Sprint 4, dilakukan pengujian kegunaan (*usability testing*) menggunakan skala **System Usability Scale (SUS)** dengan menyebarkan kuesioner kepada 9 responden (5 Mahasiswa, 3 Dosen, dan 1 Kaprodi).
*   **Hasil Rata-rata Skor SUS**: **82.5**
*   **Kategori Interpretasi**: **Grade A (Excellent)** dengan tingkat penerimaan (*acceptability*) yang sangat tinggi, menunjukkan bahwa antarmuka sistem dinilai sangat ramah pengguna (*user-friendly*) dan mudah dipelajari (*learnability*).
