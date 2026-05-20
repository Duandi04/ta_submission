# Tabel Skenario - Pengaturan TA per Program Studi

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Pengaturan TA per Program Studi (View, Update Settings) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi memodifikasi batas waktu deadline dan parameter tugas akhir (waktu mulai/selesai pengajuan, maksimal batch, kuota percobaan pengajuan per batch) spesifik untuk program studinya |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Halaman Dashboard Kaprodi |
| **Kondisi Akhir** | Konfigurasi program studi diperbarui di database (`program_studis`) dan diterapkan secara dinamis untuk mahasiswa terdaftar |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (KAPRODI) | Reaksi Sistem (SYSTEM) |
| :---: | --- | --- |
| 1 | - | Menampilkan Halaman Pengaturan Program Studi (terisi data saat ini) |
| 2 | Mengisi tanggal/waktu mulai & selesai pengajuan, batas maksimal batch, dan percobaan pengajuan per batch | - |
| 3 | Klik menyimpan konfigurasi | Memvalidasi input dan menyimpan perubahan ke tabel database `program_studis` |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **3a. Validasi Form Gagal (Format Data Tidak Valid):**
  1. Sistem mendeteksi kesalahan input (misal: waktu selesai sebelum waktu mulai, kuota bernilai negatif/kosong).
  2. Perubahan ditolak sistem dan tidak disimpan ke database.
  3. Menampilkan pesan error validasi yang sesuai (misal: *"Waktu selesai pengajuan harus setelah waktu mulai pengajuan"*).
