# Tabel Skenario - Menilai Draft Proposal Mahasiswa

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Menilai Draft Proposal Mahasiswa |
| **Aktor** | Dosen Penguji / Pembimbing |
| **Deskripsi** | Menggambarkan proses pengisian skor kriteria penilaian (rubrik) dan pemberian catatan kelayakan untuk proposal mahasiswa |
| **Kondisi Awal** | Dosen berada di halaman detail proposal mahasiswa yang siap dinilai |
| **Kondisi Akhir** | Penilaian disimpan di database, status proposal terupdate, dan mahasiswa ternotifikasi |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Dosen mengklik tombol "Berikan Penilaian" | Menampilkan Halaman Form Penilaian (berisi rubrik kriteria dan kolom komentar) |
| 2 | Dosen mengisi nilai angka pada setiap kriteria rubrik dan menuliskan komentar saran, lalu mengklik "Simpan Penilaian" | Menerima data penilaian dan memvalidasi keabsahan data (nilai berada dalam rentang minimum-maksimum) |
| 3 | - | Validasi sukses: Menyimpan rekor penilaian baru ke database `Assessment` & `AssessmentScore` |
| 4 | - | Menghitung total nilai secara otomatis dan memperbarui rata-rata nilai akhir di `ThesisSubmission` |
| 5 | - | Menampilkan pesan sukses *"Penilaian berhasil disimpan"* dan kembali ke detail proposal dengan status terbaru |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **2a. Validasi Form Gagal (Nilai Kosong / Di Luar Batas):**
  1. Sistem mendeteksi ada kriteria rubrik yang belum diisi nilainya, atau angka melebihi batas maksimum rubrik.
  2. Sistem menolak penyimpanan.
  3. Sistem menampilkan pesan peringatan merah pada kriteria yang tidak valid dan meminta dosen memperbaikinya.
