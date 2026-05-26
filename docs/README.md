# Dokumentasi Sistem Informasi Pengajuan Tugas Akhir (TA Submission)

Selamat datang di direktori dokumentasi resmi **Sistem Informasi Pengajuan Tugas Akhir (TA Submission)**. Berkas ini berfungsi sebagai peta navigasi utama untuk memandu Anda memahami arsitektur, basis data, aturan keamanan (RBAC), alur bisnis (Activity Diagram), interaksi antarmuka (Sequence Diagram), serta skenario pengujian fungsional sistem.

---

## 🗺️ 1. Peta Navigasi Diagram UML & Skenario Fitur (Berdasarkan Role)

Seluruh diagram UML dan tabel skenario fitur telah dikelompokkan secara terstruktur pertama berdasarkan **Jenis Diagram** kemudian dibagi berdasarkan **Peran Pengguna (*Role*)** di bawah direktori `docs/uml/`.

### 🌐 A. Sistem Umum (Global)
Diagram yang menggambarkan struktur arsitektur data dan gambaran umum aktor sistem secara keseluruhan:
*   **[Class Diagram (Diagram Kelas)](file:///opt/lampp/htdocs/ta_submission/docs/uml/class_diagram.md)** - Hubungan antarmodel, atribut, metode, dan relasi tabel database.
*   **[Use Case Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/use_case_diagram.md)** - Peta interaksi seluruh aktor (Kaprodi, Dosen, Mahasiswa) terhadap fungsi-fungsi sistem.

---

### 👤 B. Peran: User Biasa (Otentikasi & Profil)
Fitur dasar aksesibilitas dan pengaturan akun yang berlaku untuk seluruh aktor pengguna:

| Fitur | 🎬 Sequence Diagram | 🔄 Swimlane Activity Diagram | 📝 Tabel Skenario |
| :--- | :--- | :--- | :--- |
| **Login** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/User/sequence_diagram_login.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/User/activity_diagram_login.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/User/skenario_login.md) |
| **Logout** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/User/sequence_diagram_logout.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/User/activity_diagram_logout.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/User/skenario_logout.md) |
| **Ganti Password** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/User/sequence_diagram_ganti_password.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/User/activity_diagram_ganti_password.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/User/skenario_ganti_password.md) |
| **Edit Profile** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/User/sequence_diagram_edit_profile.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/User/activity_diagram_edit_profile.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/User/skenario_edit_profile.md) |

---

### 🎓 C. Peran: Mahasiswa
Fitur bagi mahasiswa untuk mengajukan proposal tugas akhir dan memperbarui draf:

| Fitur | 🎬 Sequence Diagram | 🔄 Swimlane Activity Diagram | 📝 Tabel Skenario |
| :--- | :--- | :--- | :--- |
| **Pengajuan Tugas Akhir** | *(Tergabung dalam kelola detail)* | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Mahasiswa/activity_diagram_pengajuan.md) | *(Tergabung dalam kelola detail)* |
| **Kelola Detail Pengajuan** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Mahasiswa/sequence_diagram_kelola_detail_pengajuan.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Mahasiswa/activity_diagram_kelola_detail_pengajuan.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Mahasiswa/skenario_kelola_detail_pengajuan.md) |

---

### 👨‍🏫 D. Peran: Dosen (Pembimbing & Penguji)
Fitur bagi dosen pembimbing untuk memantau bimbingan dan dosen penguji untuk menilai ujian:

| Fitur | 🎬 Sequence Diagram | 🔄 Swimlane Activity Diagram | 📝 Tabel Skenario |
| :--- | :--- | :--- | :--- |
| **Tinjauan Bimbingan** | *(Menggunakan alur lihat bimbingan)* | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Dosen/activity_diagram_bimbingan.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Dosen/skenario_lihat_proposal_mahasiswa_bimbingan.md) |
| **Lihat Mahasiswa Bimbingan** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Dosen/sequence_diagram_lihat_proposal_mahasiswa_bimbingan.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Dosen/activity_diagram_lihat_proposal_mahasiswa_bimbingan.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Dosen/skenario_lihat_proposal_mahasiswa_bimbingan.md) |
| **Menilai Draft Proposal** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Dosen/sequence_diagram_menilai_draft_proposal_mahasiswa.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Dosen/activity_diagram_menilai_draft_proposal_mahasiswa.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Dosen/skenario_menilai_draft_proposal_mahasiswa.md) |
| **Sidang & Penilaian Ujian** | *(Tergabung dalam menilai draft)* | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Dosen/activity_diagram_sidang_penilaian.md) | *(Tergabung dalam menilai draft)* |

---

### 👑 E. Peran: Ketua Program Studi (Kaprodi)
Fitur manajerial dan administratif tertinggi untuk mengelola seluruh data master sistem dan kelulusan mahasiswa:

| Fitur | 🎬 Sequence Diagram | 🔄 Swimlane Activity Diagram | 📝 Tabel Skenario |
| :--- | :--- | :--- | :--- |
| **Kelola Dosen** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_dosen.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_kelola_dosen.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_kelola_dosen.md) |
| **Kelola Mahasiswa** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_mahasiswa.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_kelola_mahasiswa.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_kelola_mahasiswa.md) |
| **Kelola Daftar Pengajuan** | [Utama](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md) <br> **Riwayat:** [Tambah](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_riwayat_tambah.md) \| [Edit](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_riwayat_edit.md) <br> **Awal:** [Atur Penilai](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_atur_penilai.md) \| [Tolak](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_tolak_awal.md) <br> **Akhir:** [Pantau](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_pantau_nilai.md) \| [Terima](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_terima.md) \| [Tolak](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan_tolak_akhir.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_kelola_daftar_pengajuan.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_kelola_daftar_pengajuan.md) |
| **Kelola Rubrik Penilaian** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_rubrik_penilaian.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_kelola_rubrik_penilaian.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_kelola_rubrik_penilaian.md) |
| **Pengaturan Sistem TA** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_pengaturan_ta.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_pengaturan_ta.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_pengaturan_ta.md) |
| **Cetak Laporan Proposal** | [Sequence Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_cetak_laporan_pengajuan_proposal.md) | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_cetak_laporan_pengajuan_proposal.md) | [Tabel Skenario](file:///opt/lampp/htdocs/ta_submission/docs/uml/Tabel%20Skenario/Kaprodi/skenario_cetak_laporan_pengajuan_proposal.md) |
| **Penjadwalan Sidang** | *(Menggunakan alur kelola pengajuan)* | [Activity Diagram](file:///opt/lampp/htdocs/ta_submission/docs/uml/Activity%20Diagram/Kaprodi/activity_diagram_penjadwalan.md) | *(Menggunakan alur kelola pengajuan)* |

---

## 📕 2. Panduan & Dokumen Konfigurasi Teknis Sistem

Selain diagram visual, berikut adalah dokumentasi tertulis mengenai cara kerja internal dan panduan fungsional aplikasi:

1.  **[Arsitektur Kode & Aplikasi](file:///opt/lampp/htdocs/ta_submission/docs/ARCHITECTURE.md)** - Penjelasan mengenai pola desain MVC, *routing*, repositori layanan, dan struktur folder Laravel yang digunakan dalam sistem ini.
2.  **[Panduan RBAC (Role-Based Access Control)](file:///opt/lampp/htdocs/ta_submission/docs/RBAC_GUIDE.md)** - Konfigurasi hak akses pengguna menggunakan Laravel Spatie Permissions (Kaprodi, Dosen, Mahasiswa).
3.  **[Fitur & Kontrol Manajerial Kaprodi](file:///opt/lampp/htdocs/ta_submission/docs/FEATURES_KAPRODI.md)** - Panduan operasional mengenai menu-menu administratif yang dimiliki oleh Ketua Program Studi.
4.  **[Panduan Ekspor & Impor Excel](file:///opt/lampp/htdocs/ta_submission/docs/excel_import_export_guide.md)** - Aturan format data berkas spreadsheet untuk impor massal data dosen dan mahasiswa.
5.  **[Sistem Pengecekan Similaritas Berkas](file:///opt/lampp/htdocs/ta_submission/docs/SIMILARITY_CHECK.md)** - Cara kerja internal integrasi deteksi plagiarisme proposal tugas akhir.
6.  **[Panduan Konfigurasi Sistem](file:///opt/lampp/htdocs/ta_submission/docs/CONFIGURATION.md)** - Langkah pengaturan variabel lingkungan (`.env`), *database seeding*, dan pemasangan sistem di server lokal.
7.  **[Panduan Pengujian (Testing)](file:///opt/lampp/htdocs/ta_submission/docs/TESTING_GUIDE.md)** - Cara menjalankan pengujian unit (*Unit Testing*) dan fungsional untuk memverifikasi keamanan dan reliabilitas alur kode.

---

## ✍️ 3. Pedoman Pembacaan & Pemeliharaan Dokumen

1.  **Penggunaan di Skripsi**: Kami merekomendasikan menyalin alur diagram teks Mermaid yang ada di berkas `.md` ke dalam aplikasi menggambar seperti **Draw.io** atau **Miro** dengan tipe garis **Orthogonal** agar menghasilkan berkas gambar cetak (`.png`/`.svg`) dengan sudut siku lurus yang bersih untuk dicetak pada naskah buku skripsi Anda.
2.  **Pembaruan Dokumen**: Jika terdapat perubahan logika pada fungsi atau controller Laravel di kemudian hari, harap perbarui berkas `.md` yang relevan agar sinkronisasi antara kode aktual dan berkas dokumentasi tetap terjaga.
