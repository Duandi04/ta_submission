# Tabel Skenario - Kelola & Detail Pengajuan

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola & Detail Pengajuan (Create, Submit Draft, Hapus Draft) |
| **Aktor** | Mahasiswa |
| **Deskripsi** | Menggambarkan alur mahasiswa membuat pengajuan proposal baru, melihat detail draft, menyerahkan draft secara final ("Ajukan Sekarang"), atau membatalkan draf pengajuan ("Batalkan Pengajuan") |
| **Kondisi Awal** | Mahasiswa telah masuk ke sistem dan berada di Halaman Daftar Pengajuan |
| **Kondisi Akhir** | Proposal baru disimpan dengan status Draft, diserahkan ke Dosen, atau draf dibatalkan/dihapus secara permanen |

## Alur Utama (Basic Flow - Pilihan A: Membuat Pengajuan Baru)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Klik tombol "Buat Pengajuan Baru" | Menampilkan Form Pengajuan Baru |
| 2 | Mengisi judul, abstrak, mengunggah berkas proposal (.docx/.pdf), lalu klik "Kirim" | Menerima data masukan dan memvalidasi file berkas |
| 3 | - | Menyimpan data ke tabel database `thesis_submissions` dengan status **Draft** dan file ke Storage |
| 4 | - | Redirect ke Daftar Pengajuan dengan pesan sukses *"Proposal baru berhasil disimpan"* |

## Alur Utama (Basic Flow - Pilihan B: Mengajukan Draft secara Final)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Klik tombol "Detail" pada pengajuan berstatus **Draft** | Menampilkan Halaman Detail Pengajuan Draft |
| 2 | Klik tombol "Ajukan Sekarang" | Mengubah status pengajuan di database menjadi **submitted** (Diajukan) |
| 3 | - | Redirect ke Daftar Pengajuan dengan pesan sukses *"Proposal berhasil diajukan"* |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **Pilihan C. Membatalkan / Menghapus Draft Pengajuan:**
  1. Pada Halaman Detail Pengajuan Draft (Langkah 1 Pilihan B), Mahasiswa klik tombol "Batalkan Pengajuan".
  2. Sistem menghapus berkas fisik dari storage.
  3. Sistem menghapus baris data pengajuan dari database (`thesis_submissions` & `submission_files`).
  4. Redirect ke Halaman Daftar Pengajuan dengan pesan sukses *"Pengajuan berhasil dibatalkan"*.

* **Validasi Form Gagal (Format Data / File Tidak Valid):**
  1. Pada Langkah 2 Pilihan A, sistem mendeteksi berkas berformat ilegal (misal: .png) atau melebihi batas ukuran (misal: > 10MB).
  2. Sistem menampilkan pesan error validasi berwarna merah di form.
  3. Data tidak disimpan ke database dan mahasiswa tetap di halaman form.
