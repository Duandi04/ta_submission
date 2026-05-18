# Tabel Skenario - Kelola Dosen

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola Dosen (Read, Create, Update, Import, Export) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses mengelola data master Dosen, mencakup melihat daftar, menambah, mengedit data, impor massal, dan ekspor excel |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Data dosen terupdate di database, diimpor dengan sukses, atau diekspor ke file Excel |

## 1. Skenario A: Read (Melihat Daftar & Detail Dosen)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih menu "Kelola Dosen" | Mengirimkan request ke `LecturerController->index()` |
| 2 | - | Query data User ber-role 'dosen'/'kaprodi' yang sesuai dengan ID prodi Kaprodi |
| 3 | - | Menampilkan halaman Daftar Dosen lengkap dengan tabel data, tombol aksi, serta form pencarian/sorting |

---

## 2. Skenario B: Create (Menambah Dosen Baru)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Tambah Dosen" | Menampilkan Form Tambah Dosen baru |
| 2 | Kaprodi mengisi data dosen (NIP, Nama, Email, Password, Alamat, dll.) dan klik "Simpan" | Memvalidasi input data form (NIP/Email harus unik) |
| 3 | - | Validasi Sukses: Membuat record user baru di database dan memberikan role 'dosen' |
| 4 | - | Mengalihkan kembali ke daftar dengan pesan sukses *"Dosen berhasil ditambahkan"* |

---

## 3. Skenario C: Update (Mengubah Data Dosen)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih dosen tertentu di tabel daftar lalu klik "Edit" | Menampilkan Form Edit Dosen berisi data dosen terkait |
| 2 | Kaprodi memperbarui kolom data dan menekan tombol "Perbarui" | Memvalidasi data yang diubah |
| 3 | - | Validasi Sukses: Menyimpan perubahan data ke record user di Database |
| 4 | - | Mengalihkan kembali ke daftar dengan pesan sukses *"Dosen berhasil diperbarui"* |

---

## 4. Skenario D: Import Dosen (Impor Massal Excel/CSV)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Import Dosen" dan memilih file Excel/CSV, lalu klik "Unggah" | Menerima berkas file excel dan memicu parser `Excel::import(new UserImport('dosen'))` |
| 2 | - | Mengurai baris file satu per satu, memvalidasi integritas data, lalu membuat user masal |
| 3 | - | Mengalihkan halaman kembali dengan notifikasi sukses *"Data dosen berhasil diimpor"* |

---

## 5. Skenario E: Export Dosen (Ekspor ke Excel)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Export Excel" | Mengirimkan request ekspor ke `LecturerController->export()` |
| 2 | - | Query data dosen prodi bersangkutan, memformat data ke format spreadsheet |
| 3 | - | Menghasilkan berkas `.xlsx` dan mengirimkan respon download otomatis ke browser Kaprodi |
