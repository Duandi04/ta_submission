# Tabel Skenario - Kelola Detail Pengajuan

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola Detail Pengajuan |
| **Aktor** | Mahasiswa |
| **Deskripsi** | Menggambarkan alur mahasiswa memodifikasi draf proposal, mengunggah revisi berkas proposal, atau mengubah judul dan abstrak |
| **Kondisi Awal** | Mahasiswa telah membuat pengajuan tugas akhir sebelumnya dan berada di Halaman Detail Pengajuan |
| **Kondisi Akhir** | Perubahan detail pengajuan dan berkas baru tersimpan di database |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Mahasiswa memilih tombol "Ubah Pengajuan" | Menampilkan Form Edit Pengajuan terisi judul dan abstrak lama |
| 2 | Mahasiswa mengubah judul/abstrak, mengunggah file proposal terbaru (.docx/.pdf), lalu menekan tombol "Simpan Perubahan" | Menerima data masukan dan memvalidasi file berkas (tipe file, ukuran berkas) |
| 3 | - | Validasi sukses: Memperbarui record judul/abstrak di model `ThesisSubmission` |
| 4 | - | Menyimpan file baru ke penyimpanan lokal (`Storage`) dan memperbarui record di `SubmissionFile` |
| 5 | - | Menampilkan pesan sukses *"Pengajuan berhasil diperbarui"* dan menampilkan detail terbaru |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **2a. Validasi File Gagal (Ukuran Terlalu Besar / Ekstensi Salah):**
  1. Sistem mendeteksi berkas yang diunggah berformat ilegal (misal: .png) atau melebihi batas ukuran (misal: > 10MB).
  2. Sistem menampilkan pesan error *"Format file harus PDF/Word"* atau *"Ukuran file maksimal 10MB"*.
  3. Perubahan tidak disimpan dan mahasiswa dikembalikan ke form isian.
