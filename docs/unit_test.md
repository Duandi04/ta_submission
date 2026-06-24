# Dokumentasi Automated Testing - Sistem Pengajuan Tugas Akhir

Sistem Informasi Pengajuan Tugas Akhir (TA Submission) menggunakan kerangka kerja pengujian **PHPUnit** pada Laravel untuk memverifikasi logika bisnis, relasi data, hak akses, dan stabilitas fungsionalitas secara otomatis.

---

## 📊 1. Ringkasan Eksekusi Pengujian
Pengujian otomatis dijalankan pada lingkungan SQLite (*in-memory database*) untuk memastikan kecepatan eksekusi dan isolasi data.

*   **Total Kasus Uji (Tests)**: 183 passed
*   **Total Asersi (Assertions)**: 611 assertions
*   **Durasi Eksekusi**: 169.42 detik
*   **Komando Eksekusi**: `php artisan test`

---

## 🛠️ 2. Analisis Unit Testing (47 Tests)
Unit Testing berfokus pada validasi model, service logic, helper, dan scope kueri tanpa meluncurkan HTTP request.

### A. [AssessmentServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/AssessmentServiceTest.php) (6 Tests)
Menguji backend service yang menangani pengolahan kriteria rubrik dan penyimpanan skor penilaian dosen:
*   `test_get_examiner_assessments`: Memastikan pengambilan penilaian yang ditugaskan kepada Dosen Penilai (*Examiner*) tertentu berjalan tepat.
*   `test_get_criteria`: Memastikan sistem dapat membaca butir kriteria aktif dari rubrik penilaian.
*   `test_get_criteria_for_assessment_with_snapshot`: Memvalidasi pembacaan kriteria rubrik dari data snapshot riwayat penilaian (untuk menjaga konsistensi nilai jika rubrik induk diubah).
*   `test_find_existing_assessment`: Memastikan pencarian penugasan penilaian yang telah terdaftar berjalan valid.
*   `test_create_and_update_assessment`: Menguji logika simpan dan perbaruan data nilai numerik per kriteria rubrik.
*   `test_can_edit`: Memverifikasi aturan bisnis apakah dosen penilai masih diperbolehkan mengubah nilai (hanya sebelum disubmit secara final).

### B. [CoordinatorServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/CoordinatorServiceTest.php) (2 Tests)
Menguji pengambilan data oleh Koordinator Tugas Akhir:
*   `test_get_all_submissions`: Memastikan koordinator dapat menarik seluruh pengajuan proposal mahasiswa di bawah program studinya.
*   `test_get_submission`: Memastikan pengambilan detail proposal tertentu berdasarkan ID berjalan valid.

### C. [DashboardServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/DashboardServiceTest.php) (5 Tests)
Menguji logika pengambilan statistik ringkasan dashboard untuk masing-masing dari 5 peran utama:
*   `test_get_stats_for_admin`: Statistik pengguna aktif, fakultas, dan program studi.
*   `test_get_stats_for_kaprodi`: Metrik proposal masuk, disetujui, ditolak, dan bimbingan aktif.
*   `test_get_stats_for_coordinator`: Metrik monitoring pengusul proposal Tugas Akhir.
*   `test_get_stats_for_dosen`: Metrik jumlah mahasiswa bimbingan dan draf proposal yang harus dinilai.
*   `test_get_stats_for_student`: Status proposal aktif milik mahasiswa yang sedang login.

### D. [KaprodiServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/KaprodiServiceTest.php) (2 Tests)
Menguji aturan bisnis utama yang dikelola oleh Ketua Program Studi:
*   `test_accept_submission_prevents_multiple_approvals`: Menegakkan aturan bahwa mahasiswa yang sudah memiliki satu proposal disetujui (`approved`) dilarang keras disetujui pengajuannya yang lain (sistem wajib melempar eksepsi).
*   `test_get_students_query_includes_required_data`: Memastikan kueri data mahasiswa memuat metrik jumlah proposal (`thesis_submissions_count`) dan memuat relasi data proposal disetujui secara tepat.

### E. [SubmissionServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/SubmissionServiceTest.php) (12 Tests)
Menguji alur pengusulan proposal mahasiswa beserta penegakan batas aturan akademik:
*   `test_get_student_submissions`: Memvalidasi kueri dan pagination riwayat pengajuan milik mahasiswa.
*   `test_create_submission_success`: Menguji pembuatan draf pengajuan baru beserta pengunggahan berkas biner PDF.
*   `test_create_submission_fails_when_before_start_deadline`: Memastikan sistem menolak pengajuan baru jika tanggal saat ini mendahului tanggal mulai periode pengajuan yang diatur Kaprodi.
*   `test_create_submission_fails_when_after_end_deadline`: Memastikan sistem memblokir pengajuan jika periode pengajuan telah ditutup.
*   `test_create_submission_fails_when_has_approved_submission`: Memastikan penolakan draf jika mahasiswa sudah lulus pengajuan.
*   `test_create_submission_fails_when_max_total_limit_reached`: Menguji batas maksimal pengusulan judul (attempts_per_batch * max_batches).
*   `test_create_submission_fails_when_current_batch_not_all_finished`: Memastikan mahasiswa menyelesaikan judul sebelumnya (ditolak/batal) terlebih dahulu sebelum mengusulkan batch baru.
*   `test_create_submission_allows_exceeding_limit`: Menguji flag override `can_exceed_submission_limit` bagi mahasiswa tertentu agar tetap dapat mengajukan proposal meski kuota habis.
*   `test_update_submission_success`: Menguji pembaruan teks judul, abstrak, dan penggantian file PDF lama dengan file baru.
*   `test_delete_submission`: Memastikan fungsi penghapusan draf memicu soft delete.
*   `test_can_edit` & `test_can_delete`: Menegakkan hak status (draf hanya boleh dimodifikasi jika statusnya `draft` atau `revision_required`).

### F. [SupervisorServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/SupervisorServiceTest.php) (4 Tests)
Menguji logika akses bimbingan dosen pembimbing:
*   `test_get_supervised_students`: Menarik kueri daftar mahasiswa yang dibimbing.
*   `test_get_student_submissions`: Menarik kueri daftar pengajuan dari anak bimbingan.
*   `test_get_submission_success`: Dosen pembimbing sah dapat melihat detail proposal bimbingannya.
*   `test_get_submission_denied_for_non_supervisor`: Memastikan sistem menolak akses dosen lain yang tidak ditunjuk sebagai pembimbing proposal mahasiswa tersebut.

### G. [ThesisSubmissionTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/ThesisSubmissionTest.php) (15 Tests)
Menguji integritas relasi Eloquent ORM pada model `ThesisSubmission`:
*   `test_can_create_thesis_submission`: Pengujian pembuatan record instan melalui factory.
*   `test_thesis_belongs_to_student`: Relasi *Belongs To* ke entitas Mahasiswa (`student`).
*   `test_thesis_belongs_to_supervisor`: Relasi *Belongs To* ke Dosen Pembimbing (`supervisor`).
*   `test_thesis_has_many_files`: Relasi *Has Many* ke berkas proposal (`files`).
*   `test_thesis_has_many_assessments`: Relasi ke penilaian dosen (`assessments`).
*   `test_thesis_has_many_comments`: Relasi ke log komentar bimbingan (`comments`).
*   `test_thesis_has_many_statuses`: Relasi ke riwayat status (`statuses`).
*   `test_get_status_badge_class`: Memverifikasi keakuratan pemetaan warna badge Tailwind/Bootstrap (misal: draft -> secondary, approved -> success, under_review -> warning).
*   `test_get_status_label`: Memverifikasi keakuratan label Bahasa Indonesia untuk status (misal: submitted -> "Sudah Diajukan").
*   `test_can_be_edited_by_student`: Menegakkan status yang dapat diedit oleh mahasiswa.
*   `test_scope_by_status`, `test_scope_by_student`, `test_scope_by_supervisor`: Memvalidasi performa query scopes pembatas data.
*   `test_get_latest_file`: Memastikan pengambilan berkas fisik proposal teranyar (versi revisi terakhir).
*   `test_soft_delete_works`: Memastikan baris data terhapus secara logis dan dapat dipanggil lewat `withTrashed()`.
*   `test_factory_states`: Memvalidasi pembentukan data otomatis model dengan state draft, submitted, atau completed.

---

## 🌐 3. Analisis Feature Testing (136 Tests)
Feature Testing mensimulasikan siklus HTTP Request nyata (GET, POST, PUT, DELETE), pengiriman data form input, session handling, enkripsi, serta validasi middleware.

### Key Feature Test Suites:

1.  **[AuthTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/AuthTest.php) (5 Tests)**:
    *   Memastikan form login dirender sempurna.
    *   Menguji keberhasilan autentikasi menggunakan email & sandi valid, disusul pengalihan sesi ke rute dashboard.
    *   Memastikan akun non-aktif (`is_active = false`) dilarang masuk.
    *   Menguji keberhasilan penghancuran session saat logout.
2.  **[UserProfileTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/UserProfileTest.php) (4 Tests)**:
    *   Mengakses halaman ubah profil mahasiswa.
    *   Memastikan pembatasan bahwa data sensitif akademik (seperti email dan angkatan) tidak boleh diubah oleh mahasiswa, melainkan hanya info kontak (nomor telepon).
    *   Menguji penggantian password melalui validasi password saat ini (`Hash::check`).
    *   Menguji fungsionalitas unggah dan hapus foto profil menggunakan `Storage::fake('local')`.
3.  **[RbacControlTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/RbacControlTest.php) (7 Tests)**:
    *   Menguji halaman panel Admin RBAC.
    *   Memastikan Admin dapat membuat peran baru dan memetakan izin akses (*permissions*).
    *   Mengunci peran bawaan sistem (Kaprodi, Dosen, Mahasiswa) agar tidak dapat diganti namanya atau dihapus oleh administrator untuk mencegah kerusakan sistem.
    *   Menguji kebenaran pemetaan rute dinamis berbasis izin akses.
4.  **[FileSimilarityTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/FileSimilarityTest.php) (4 Tests)**:
    *   Memastikan mahasiswa dapat mengunduh berkas lamaran proposalnya sendiri.
    *   Memastikan upaya mahasiswa lain mengintip/mengunduh berkas privat tersebut ditolak dengan respons **403 Forbidden**.
    *   Memastikan dosen pembimbing/kaprodi memiliki izin penuh mengunduh berkas mahasiswa.
    *   Menguji keakuratan respons JSON API pengecekan kemiripan judul `/api/similarity-check`.
5.  **[KaprodiManagementTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/KaprodiManagementTest.php) (5 Tests)**:
    *   Menguji kemampuan Kaprodi dalam CRUD Rubrik Penilaian beserta butir kriterianya secara dinamis.
    *   Menguji pengaturan tenggat periode pengajuan.
    *   Menguji pengelolaan dosen pembimbing dan dosen penilai.
    *   Menguji impor massal dosen pembimbing melalui format berkas CSV.
6.  **[StudentSubmissionControllerTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/StudentSubmissionControllerTest.php) (20 Tests)**:
    *   Menguji lengkap seluruh interaksi mahasiswa terhadap proposalnya: buat draf, sunting, hapus, batalkan, dan ajukan final.
    *   Memvalidasi penegakan deteksi kemiripan real-time dan validasi tenggat waktu periode TA.
7.  **[KaprodiStudentControllerTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/KaprodiStudentControllerTest.php) (8 Tests)**:
    *   Menguji CRUD data mahasiswa secara administratif oleh Kaprodi.
    *   Menguji kelancaran ekspor data mahasiswa ke format Excel.
    *   Menguji parser impor data mahasiswa secara massal menggunakan file Excel.
8.  **[KaprodiStudentManagementTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/KaprodiStudentManagementTest.php) (4 Tests)**:
    *   Menguji halaman rekap mahasiswa milik Kaprodi.
    *   Memverifikasi filter laporan kelayakan proposal (hanya menampilkan mahasiswa yang proposalnya disetujui).
    *   Memvalidasi sorting (pengurutan) tabel mahasiswa berdasarkan NIM secara alfabetis.
9.  **[CommentTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/CommentTest.php) (12 Tests)**:
    *   Menguji fungsionalitas log komentar bimbingan.
    *   Memastikan komentar dan balasan bertingkat (*nested comments*) terpetakan dengan benar dan diurutkan secara kronologis (dari yang terlama ke terbaru).
10. **[DosenControllersTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/DosenControllersTest.php) (12 Tests)**:
    *   Mensimulasikan portal dosen: melihat daftar mahasiswa bimbingan, meninjau berkas, mengisi nilai rubrik numerik, menyimpan draf nilai, hingga mengunci nilai secara permanen.
11. **[DashboardControllerTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/DashboardControllerTest.php) (5 Tests)**:
    *   Memastikan keberhasilan render HTML dasbor tanpa galat bagi kelima aktor pengguna.
