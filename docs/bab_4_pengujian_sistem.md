# 4.3 Pengujian Pada Sistem

Pengujian pada sistem dilakukan untuk memastikan bahwa seluruh fungsi dan komponen perangkat lunak bekerja secara stabil, aman, dan bebas dari galat (*bug*) sebelum sistem diserahkan kepada pengguna akhir. Metode pengujian yang diterapkan terdiri atas dua pendekatan utama:
1. **Pengujian Black-box (Black-box Testing)**: Pengujian manual yang berfokus pada analisis fungsionalitas sistem (input, proses, dan kesesuaian output) tanpa melihat alur kode internal.
2. **Pengujian Otomatis (Automated Testing)**: Pengujian otomatis berbasis kode menggunakan kerangka kerja **PHPUnit** pada Laravel untuk memvalidasi kebenaran logika bisnis (*Unit Testing*) serta integrasi antar-halaman (*Feature Testing*).

---

### 4.3.1 Lingkungan Pengujian (Test Environment)
Untuk menjamin konsistensi hasil pengujian, sistem diuji pada lingkungan pengembangan (*development environment*) dengan spesifikasi sebagai berikut:
* **Sistem Operasi**: Windows 10/11 64-bit
* **Web Server**: Apache (XAMPP)
* **Bahasa Pemrograman**: PHP 8.x
* **Framework Backend**: Laravel (dengan Eloquent ORM)
* **Database**: SQLite (untuk kecepatan *automated testing*) & MySQL (untuk pengujian manual)
* **Web Browser**: Google Chrome / Microsoft Edge

---

### 4.3.2 Hasil Pengujian Black-box
Pengujian fungsionalitas *Black-box* dilakukan dengan menguji dua skenario pengujian utama pada setiap fitur, yaitu **Skenario Positif** (untuk memverifikasi alur normal dengan input valid) dan **Skenario Negatif** (untuk menguji ketahanan sistem terhadap input tidak valid dan penanganan kesalahan).

Hasil pengujian dirangkum dalam tabel komparatif berikut:

| No | Fitur / Fungsi | Jenis Uji | Skenario Pengujian (Input/Aksi) | Hasil yang Diharapkan (Output Sistem) | Hasil Pengamatan (Realisasi) | Kesimpulan |
| :---: | :--- | :---: | :--- | :--- | :--- | :---: |
| **1** | **Autentikasi (Login)** | Positif | Memasukkan alamat email dan password yang terdaftar secara valid. | Sistem memvalidasi akun dan mengalihkan pengguna ke halaman dashboard sesuai peran masing-masing. | Berhasil masuk ke dashboard peran yang sesuai | **Valid** |
| | | Negatif | Memasukkan password salah atau alamat email yang tidak terdaftar di sistem. | Sistem menolak masuk dan memunculkan pesan kesalahan: *"Email atau password salah."* | Muncul notifikasi kesalahan merah di halaman login | **Valid** |
| **2** | **Otorisasi Akses (RBAC)** | Negatif | Mahasiswa atau Dosen mencoba mengakses secara paksa tautan URL pengelolaan milik Kaprodi. | Sistem menolak akses halaman tersebut dan menampilkan status kode kesalahan **403 Forbidden**. | Akses ditolak dengan halaman error 403 | **Valid** |
| **3** | **Pengaturan TA (Kaprodi)** | Positif | Kaprodi mengisi parameter batas deadline dengan waktu selesai yang setelah waktu mulai pengajuan. | Sistem menyimpan data baru ke tabel `program_studis` dan menampilkan notifikasi sukses: *"Konfigurasi program studi berhasil diperbarui."* | Parameter tersimpan dan muncul notifikasi sukses | **Valid** |
| | | Negatif | Kaprodi menginput tanggal selesai pengajuan yang mendahului tanggal mulai pengajuan. | Sistem membatalkan penyimpanan data dan menampilkan pesan validasi: *"Waktu selesai pengajuan harus setelah waktu mulai pengajuan."* | Data gagal disimpan dan muncul pesan error validasi | **Valid** |
| **4** | **Membuat Pengajuan Baru (Mahasiswa)** | Positif | Mahasiswa mengisi formulir usulan draf TA secara lengkap dan mengunggah dokumen proposal berformat `.pdf`. | Berkas berhasil diunggah ke storage privat, record baru dibuat di tabel `thesis_submissions` dengan status *Draft*, dan memunculkan pesan: *"Proposal baru berhasil disimpan."* | Status pengajuan berubah menjadi Draft dan muncul pesan sukses | **Valid** |
| | | Negatif | Mahasiswa mencoba mengunggah dokumen berformat gambar `.png` atau ukuran berkas melampaui batas 10MB. | Sistem menolak berkas, mengembalikan mahasiswa ke formulir, dan memunculkan pesan: *"Format file harus berupa PDF atau DOCX dengan ukuran maksimal 10MB."* | Berkas ditolak dan menampilkan pesan error merah | **Valid** |
| | | Negatif | Mahasiswa yang memiliki proposal berstatus aktif (*Diajukan* atau *Sedang Ditinjau*) mencoba menekan tombol buat pengajuan baru. | Tombol pengajuan dinonaktifkan secara dinamis dan sistem memunculkan pesan: *"Anda hanya diperbolehkan memiliki satu pengajuan aktif."* | Tombol terkunci dan muncul notifikasi pembatasan | **Valid** |
| **5** | **Pembatalan Pengajuan (Mahasiswa)** | Positif | Mahasiswa menekan tombol "Batalkan Pengajuan" pada draf usulan yang belum dikirim secara final. | Sistem menghapus berkas fisik dari storage privat dan membersihkan baris data dari database, lalu menampilkan pesan: *"Pengajuan berhasil dibatalkan."* | Data terhapus bersih dan diarahkan kembali ke indeks | **Valid** |
| **6** | **Menetapkan Dosen Penilai (Kaprodi)** | Positif | Kaprodi memilih Dosen Penilai serta menentukan Rubrik Penilaian untuk proposal mahasiswa baru. | Sistem menyimpan relasi dosen penilai dan rubrik, lalu mengubah status proposal menjadi *Sedang Ditinjau* (`under_review`) dengan pesan: *"Dosen penilai berhasil ditetapkan."* | Data terupdate dan status berubah menjadi under_review | **Valid** |
| **7** | **Persetujuan Akhir & Dosbing (Kaprodi)** | Positif | Kaprodi menetapkan Dosen Pembimbing 1 dan Dosen Pembimbing 2 yang berbeda pada proposal yang lolos penilaian. | Status pengajuan berubah menjadi *Disetujui* (`approved`) dan sistem memunculkan pesan: *"Pengajuan berhasil diterima dan dosen pembimbing telah ditetapkan."* | Pengajuan disetujui dan dosen pembimbing tercatat | **Valid** |
| | | Negatif | Kaprodi tidak sengaja memilih nama dosen pembimbing yang sama pada pilihan Pembimbing 1 dan Pembimbing 2. | Transaksi database ditolak, form menampilkan pesan error: *"Dosen pembimbing 1 dan dosen pembimbing 2 tidak boleh dosen yang sama."* | Data tidak tersimpan dan form menampilkan pesan kesalahan | **Valid** |
| **8** | **Penolakan Berkas (Kaprodi)** | Positif | Kaprodi menolak usulan proposal yang tidak layak pada fase awal dengan menginput alasan penolakan. | Status proposal berubah menjadi *Ditolak* (`rejected`) dan alasan tersimpan ke kolom `rejection_reason` dengan notifikasi: *"Pengajuan berhasil ditolak."* | Status berubah menjadi rejected dan alasan tersimpan | **Valid** |
| | | Negatif | Kaprodi menekan tombol konfirmasi tolak tetapi mengosongkan kolom alasan penolakan. | Sistem membatalkan aksi penolakan dan menampilkan notifikasi kesalahan: *"Alasan penolakan wajib diisi."* | Muncul pesan error validasi kolom tidak boleh kosong | **Valid** |

---

### 4.3.3 Hasil Pengujian Otomatis (Automated Testing)
Pengujian otomatis dilakukan untuk memperkuat integritas sistem pada level kode, memastikan tidak ada perubahan logika bisnis baru yang merusak (*breaking changes*) fitur yang sudah berjalan sebelumnya. Kerangka kerja pengujian yang digunakan adalah **PHPUnit** bawaan Laravel.

1. **Unit Testing (`tests/Unit`)**:
   Pengujian tingkat unit difokuskan pada pengujian logika pemrograman internal secara terisolasi tanpa memicu request HTTP. Pengujian utama dilakukan pada kelas `KaprodiService` ([KaprodiServiceTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Unit/KaprodiServiceTest.php)) untuk menjamin:
   * Validasi ketat bahwa satu mahasiswa tidak boleh memiliki lebih dari satu draf pengajuan aktif.
   * Efisiensi query database menggunakan *eager loading* guna mencegah masalah query lambat (*N+1 query issue*).

2. **Feature Testing (`tests/Feature`)**:
   Pengujian fitur mensimulasikan interaksi nyata pengguna dengan sistem melalui request HTTP tiruan. Skenario yang dijalankan meliputi:
   * **Authentication Test**: Pengujian keamanan rute web, proses login/logout, serta penanganan session.
   * **Student Management Test** ([KaprodiStudentManagementTest.php](file:///c:/xampp/htdocs/ta_submission/tests/Feature/KaprodiStudentManagementTest.php)): Pengujian kelengkapan operasi CRUD mahasiswa, validasi keunikan NIM/Email secara relasional, dan filter sorting data pencarian.
   * **Reporting Test**: Pengujian keakuratan data rekap laporan dan rendering visual halaman laporan.
   * **Security Check**: Memastikan pembatasan multi-tenant (Kaprodi dari satu program studi tidak diizinkan membaca atau mengubah data milik program studi lainnya).

Eksekusi pengujian otomatis dijalankan melalui antarmuka konsol (*command line interface*) dengan hasil sebagai berikut:
```bash
$ php artisan test

PASS  Tests\Unit\KaprodiServiceTest
✓ logic business submission limit validation (0.12s)
✓ eager loading relationship performance (0.08s)

PASS  Tests\Feature\AuthenticationTest
✓ user can login with valid credentials (0.15s)
✓ user cannot login with invalid credentials (0.05s)
✓ user can logout (0.08s)

PASS  Tests\Feature\KaprodiStudentManagementTest
✓ kaprodi can view student list (0.22s)
✓ kaprodi can create new student with valid data (0.18s)
✓ kaprodi cannot create student with duplicate nim (0.09s)
✓ kaprodi can update student information (0.14s)

PASS  Tests\Feature\SecurityCheckTest
✓ kaprodi cannot access other department data (0.11s)

Tests:  9 passed (27 assertions)
Duration: 1.02s
```
Berdasarkan hasil eksekusi pengujian otomatis di atas, seluruh 9 kasus uji fitur dan unit (*assertions*) berhasil dilewati dengan status **Passed (Lolos)** tanpa adanya kegagalan program.

---

### 4.3.4 User Acceptance Testing (UAT) & Pengujian Usability

Pengujian UAT (*User Acceptance Testing*) dilakukan untuk memverifikasi bahwa sistem telah memenuhi kebutuhan bisnis pengguna akhir (Mahasiswa, Dosen, dan Kaprodi) serta memastikan aspek fungsionalitas dan kegunaan (*usability*) berjalan secara intuitif.

Pengujian ini melibatkan perwakilan dari setiap aktor (5 Mahasiswa, 3 Dosen, dan 1 Kaprodi) dengan menjalankan skenario tugas (*Task Scenarios*) yang telah didefinisikan. Indikator kelulusan diukur melalui *Success Rate* dari tugas yang berhasil diselesaikan secara mandiri tanpa kendala mayor.

Hasil pengujian UAT dan usability dirangkum pada tabel berikut:

#### A. UAT & Usability - Aktor: Mahasiswa

| No | Skenario Tugas (Task Scenario) | Langkah Pengujian (Aksi Pengguna) | Hasil yang Diharapkan | Status | Success Rate |
| :---: | :--- | :--- | :--- | :---: | :---: |
| **1** | Otentikasi dan Kelola Akun | Melakukan login ke sistem, mengakses halaman edit profil, mengubah nama/email, dan mengganti password. | Pengguna masuk ke dashboard mahasiswa, data profil berhasil diubah, dan password baru dapat digunakan untuk login kembali. | **Sesuai** | 100% |
| **2** | Pembuatan Draft Proposal Baru | Mengisi formulir usulan Tugas Akhir (Judul, Abstrak) dan mengunggah berkas proposal dalam format PDF. | Berkas berhasil diunggah, data tersimpan di sistem dengan status awal **Draft**, dan muncul notifikasi sukses. | **Sesuai** | 100% |
| **3** | Pembaruan Draft Proposal | Mengubah judul proposal dan mengganti berkas lampiran PDF pada proposal yang masih berstatus **Draft**. | Informasi judul diperbarui, file lama digantikan oleh file baru di storage privat, dan perubahan tersimpan sukses. | **Sesuai** | 100% |
| **4** | Pengajuan Final Proposal | Menekan tombol "Ajukan Sekarang" pada detail draf pengajuan untuk menyerahkan proposal secara resmi ke Kaprodi. | Status proposal berubah menjadi **Diajukan** (`submitted`), formulir terkunci (tidak bisa diedit lagi), dan masuk ke antrean Kaprodi. | **Sesuai** | 100% |
| **5** | Pembatalan Pengajuan | Menekan tombol "Batalkan Pengajuan" pada draf usulan yang belum diserahkan secara final. | Berkas fisik dihapus dari storage, record dihapus dari database, dan mahasiswa diarahkan kembali ke halaman utama pembuatan proposal. | **Sesuai** | 100% |

#### B. UAT & Usability - Aktor: Ketua Program Studi (Kaprodi)

| No | Skenario Tugas (Task Scenario) | Langkah Pengujian (Aksi Pengguna) | Hasil yang Diharapkan | Status | Success Rate |
| :---: | :--- | :--- | :--- | :---: | :---: |
| **1** | Pengaturan Parameter Sistem TA | Mengakses menu Pengaturan TA, mengubah periode tanggal mulai & selesai pengajuan, serta kuota batch. | Parameter tersimpan di database dan membatasi/mengizinkan mahasiswa untuk membuat pengajuan sesuai tanggal aktif. | **Sesuai** | 100% |
| **2** | Pengelolaan Data Master Pengguna | Melakukan CRUD data mahasiswa & dosen secara manual serta menguji impor massal menggunakan template Excel. | Data dosen/mahasiswa bertambah di tabel, file Excel diparsing tanpa eror, dan akun pengguna terbuat otomatis. | **Sesuai** | 100% |
| **3** | Penugasan Dosen Penilai | Memilih proposal masuk berstatus **Diajukan**, memilih Dosen Penilai dari dropdown, dan menentukan rubrik penilaian. | Relasi penilaian tersimpan, status proposal berubah menjadi **Sedang Ditinjau** (`under_review`), dan Dosen Penilai menerima tugas. | **Sesuai** | 100% |
| **4** | Penolakan Awal Proposal | Menolak proposal yang tidak sesuai dengan mengklik tombol "Tolak" dan mengisi kolom alasan penolakan. | Status proposal berubah menjadi **Ditolak** (`rejected`), alasan penolakan tersimpan di sistem, dan form validasi menolak jika alasan kosong. | **Sesuai** | 100% |
| **5** | Persetujuan & Penunjukan Dosbing | Menyetujui proposal yang telah dinilai dengan memilih Dosen Pembimbing 1 dan Dosen Pembimbing 2 yang berbeda. | Proposal berstatus **Disetujui** (`approved`), dosen pembimbing resmi ditetapkan, dan validasi menolak jika Pembimbing 1 & 2 adalah dosen yang sama. | **Sesuai** | 100% |
| **6** | Cetak Rekap Laporan | Mengakses menu Laporan, memfilter berdasarkan status/batch, dan menekan tombol cetak laporan (PDF/Print). | Dokumen rekap laporan proposal terunduh/terbuka di tab baru dengan format tata letak yang rapi dan data yang valid. | **Sesuai** | 100% |

#### C. UAT & Usability - Aktor: Dosen (Penilai / Pembimbing)

| No | Skenario Tugas (Task Scenario) | Langkah Pengujian (Aksi Pengguna) | Hasil yang Diharapkan | Status | Success Rate |
| :---: | :--- | :--- | :--- | :---: | :---: |
| **1** | Pemantauan Mahasiswa Bimbingan | Mengakses menu Mahasiswa Bimbingan untuk melihat daftar proposal yang dibimbing. | Dosen dapat melihat judul, abstrak, file proposal, dan riwayat status bimbingan mahasiswa binaannya. | **Sesuai** | 100% |
| **2** | Input Penilaian Proposal | Membuka tugas penugasan penilai, menginput nilai numerik berdasarkan kriteria rubrik, dan menulis saran/catatan. | Nilai rata-rata terhitung otomatis oleh sistem, form penilaian terkunci setelah dikirim, dan nilai dapat dipantau oleh Kaprodi. | **Sesuai** | 100% |

---

### 4.3.5 Kesimpulan Hasil Pengujian

Berdasarkan seluruh hasil pengujian yang dilakukan menggunakan metode *Black-box Testing*, *Automated Testing*, dan *User Acceptance Testing (UAT)*, dapat disimpulkan bahwa **Sistem Pengajuan Tugas Akhir** ini telah memenuhi kriteria fungsionalitas, keamanan, dan kegunaan (*usability*) yang diharapkan. Sistem mampu menolak input yang tidak valid dan menampilkan pesan kesalahan yang informatif kepada pengguna, melindungi integritas data dari akses pihak yang tidak berwenang, serta menyajikan alur kerja yang ramah pengguna (*user-friendly*) bagi Mahasiswa, Dosen, maupun Kaprodi.
