# Tabel Skenario - Pengaturan TA

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Pengaturan TA (View, Update Settings) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi memodifikasi aturan atau batasan tugas akhir (maksimal batch pengajuan dan maksimal pengajuan judul per batch) |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Halaman Dashboard Kaprodi |
| **Kondisi Akhir** | Konfigurasi sistem tugas akhir diperbarui di database dan diterapkan secara instan di sistem |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (KAPRODI) | Reaksi Sistem (SYSTEM) |
| :---: | --- | --- |
| 1 | - | Menampilkan Halaman Pengaturan sistem |
| 2 | Mengubah pengaturan batch sistem dan pengajuan per batch | - |
| 3 | Klik menyimpan konfigurasi | Menyimpan perubahan pengaturan sistem |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **3a. Validasi Form Gagal (Format Data Tidak Valid):**
  1. Sistem mendeteksi jumlah maksimal batch atau maksimal pengajuan diisi angka negatif/kosong atau tidak valid (kurang dari 1).
  2. Perubahan ditolak sistem dan tidak disimpan ke database.
  3. Menampilkan pesan error validasi berwarna merah di kolom input yang bermasalah.
