# Tabel Skenario - Kelola Daftar Pengajuan

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola Daftar Pengajuan |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi meninjau daftar pengajuan tugas akhir mahasiswa, menetapkan dosen pembimbing, menentukan dosen penilai & rubrik, memproses penolakan 2 fase (fase pengajuan & fase sedang ditinjau setelah dinilai), serta mengelola data history pengajuan historical. |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Pengajuan diproses (disetujui, ditolak, atau ditentukan dosen penilai/rubrik) dan data history pengajuan historical berhasil disimpan/diperbarui. |

---

## 1. Skenario A: Menyetujui Pengajuan & Menetapkan Pembimbing (Accept)
*Prasyarat: status == 'under_review' DAN semua dosen penilai telah mengirimkan nilai (allAssessed == true)*

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi membuka detail pengajuan yang berstatus *Sedang Ditinjau* (`under_review`) | Memeriksa kelayakan: Jika semua dosen penilai sudah menilai, sistem menampilkan Form **Atur Dosen Pembimbing** |
| 2 | Kaprodi memilih Dosen Pembimbing 1 dan Dosen Pembimbing 2 (Opsional) | Mengaktifkan validasi form (Dosen 1 dan 2 tidak boleh sama) |
| 3 | Kaprodi mengklik tombol "Terima Pengajuan & Tetapkan Pembimbing" | Mengirimkan request POST ke `acceptSubmission()` |
| 4 | - | Validasi Sukses: Mengubah kolom `status` pengajuan menjadi `'approved'` serta menyimpan ID pembimbing ke database |
| 5 | - | Memicu refresh halaman dan menampilkan pesan sukses *"Pengajuan berhasil diterima dan dosen pembimbing telah ditetapkan."* |

---

## 2. Skenario B: Menetapkan Dosen Penilai & Rubrik Penilaian (Assign Assessors)
*Prasyarat: status == 'submitted'*

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi membuka detail pengajuan yang berstatus *Submitted* | Menampilkan Form **Atur Dosen Penilai** dan Pilihan Rubrik Penilaian |
| 2 | Kaprodi memilih Dosen Penilai dan Rubrik yang sesuai | Mengaktifkan form |
| 3 | Kaprodi mengklik tombol "Simpan Perubahan" | Mengirimkan request POST ke `assignLecturers()` |
| 4 | - | Validasi Sukses: Menyimpan ID dosen penilai dan rubrik_id ke data pengajuan, serta mengubah status menjadi `'under_review'` |
| 5 | - | Menampilkan pesan sukses *"Dosen penilai berhasil ditetapkan."* |

---

## 3. Skenario C: Penolakan Awal (Saat Baru Diajukan / Status: Submitted)
*Prasyarat: status == 'submitted'*

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi membuka detail pengajuan yang berstatus *Submitted* | Menampilkan Form Atur Dosen Penilai beserta tombol "Tolak" |
| 2 | Kaprodi mengklik tombol "Tolak" | Menampilkan modal dialog pengisian alasan penolakan berkas |
| 3 | Kaprodi menuliskan alasan penolakan dan mengklik "Konfirmasi Tolak" | Memvalidasi kolom alasan penolakan (`rejection_reason`) wajib diisi |
| 4 | - | Validasi Sukses: Mengubah status pengajuan menjadi `'rejected'` dan menyimpan alasan ke kolom `rejection_reason` |
| 5 | - | Memicu refresh halaman dan menampilkan notifikasi *"Pengajuan berhasil ditolak."* |

---

## 4. Skenario D: Penolakan Akhir (Setelah Semua Penilai Memberikan Nilai / Status: under_review)
*Prasyarat: status == 'under_review' DAN semua dosen penilai telah mengirimkan nilai (allAssessed == true)*

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi membuka detail pengajuan berstatus *Sedang Ditinjau* (`under_review`) | Memeriksa kelayakan: Jika semua dosen penilai sudah menilai, sistem menampilkan tombol "Tolak" di bawah form pembimbing |
| 2 | Kaprodi mengklik tombol "Tolak" | Menampilkan modal dialog pengisian alasan penolakan hasil sidang / revisi mayor |
| 3 | Kaprodi menginputkan alasan ketidaklulusan sidang dan mengklik "Konfirmasi Tolak" | Memvalidasi kolom alasan (`rejection_reason`) tidak boleh kosong |
| 4 | - | Validasi Sukses: Mengubah status pengajuan menjadi `'rejected'` dan menyimpan catatan kegagalan ke kolom `rejection_reason` |
| 5 | - | Memicu refresh halaman dan menampilkan notifikasi *"Pengajuan berhasil ditolak."* |

---

## 5. Skenario E: Tambah Data History Pengajuan (Historical Submission)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Tambah Data History" | Mengalihkan halaman ke Form Tambah Data History Pengajuan (`kaprodi.submissions.create`) |
| 2 | Kaprodi mengisi Nama Mahasiswa, Judul TA, Abstrak, Bidang Riset, Dosen Pembimbing, dan Tanggal Pengajuan, lalu klik "Simpan" | Memvalidasi seluruh kolom input form |
| 3 | - | Validasi Sukses: Membuat record pengajuan baru dengan flag `is_historical = true` di database |
| 4 | - | Mengalihkan kembali ke indeks daftar pengajuan dengan notifikasi sukses *"Data history pengajuan berhasil disimpan."* |

---

## 6. Skenario F: Edit Data History Pengajuan (Historical Submission)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Edit Data History" pada pengajuan historical tertentu | Mengalihkan halaman ke Form Edit Data History Pengajuan (`kaprodi.submissions.edit_historical`) |
| 2 | Kaprodi mengubah isian judul, abstrak, atau pembimbing, lalu klik "Perbarui" | Memvalidasi data perubahan |
| 3 | - | Validasi Sukses: Memperbarui record pengajuan historical di database |
| 4 | - | Mengalihkan kembali ke detail pengajuan terkait dengan notifikasi *"Data history pengajuan berhasil diperbarui."* |
