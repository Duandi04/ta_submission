# Tabel Skenario - Pengaturan TA

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Pengaturan TA (View, Update Settings) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi memodifikasi aturan atau batasan tugas akhir (misal: kuota bimbingan dosen, tanggal batas pengajuan, atau jenis file dokumen) |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Konfigurasi sistem tugas akhir diperbarui di database dan diterapkan secara instan di sistem |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih menu "Pengaturan TA" | Mengirim request ke `KaprodiController->settings()` |
| 2 | - | Query data pengaturan aktif dari database model `Setting` |
| 3 | - | Menampilkan Halaman Form Pengaturan TA terisi dengan konfigurasi saat ini |
| 4 | Kaprodi memperbarui nilai konfigurasi (misal: mengisi batas tanggal pengajuan baru) dan menekan "Simpan Pengaturan" | Memvalidasi tipe data input (seperti validitas format tanggal) |
| 5 | - | Validasi Sukses: Memperbarui record kunci konfigurasi terkait di model `Setting` |
| 6 | - | Mengalihkan kembali ke halaman form dengan pesan sukses *"Settings updated successfully"* |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **4a. Validasi Form Gagal (Format Data Tidak Valid):**
  1. Sistem mendeteksi kuota bimbingan dosen diisi angka negatif atau tanggal batas tidak valid.
  2. Perubahan ditolak sistem dan tidak disimpan ke database.
  3. Menampilkan pesan error validasi berwarna merah di kolom input yang bermasalah.
