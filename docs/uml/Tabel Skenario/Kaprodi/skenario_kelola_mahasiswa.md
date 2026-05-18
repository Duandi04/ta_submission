# Tabel Skenario - Kelola Mahasiswa

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola Mahasiswa (Read, Create, Update, Import, Export) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses mengelola data master Mahasiswa, mencakup melihat daftar, menambah, mengedit data, impor massal, dan ekspor excel |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Data mahasiswa terupdate di database, diimpor dengan sukses, atau diekspor ke file Excel |

## 1. Skenario A: Read (Melihat Daftar & Detail Mahasiswa)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih menu "Kelola Mahasiswa" | Mengirimkan request ke `StudentController->index()` |
| 2 | - | Query data User ber-role 'mahasiswa' yang berada di bawah Program Studi Kaprodi |
| 3 | - | Menampilkan halaman Daftar Mahasiswa lengkap dengan tabel data, status pengajuan, serta form pencarian/sorting |

---

## 2. Skenario B: Create (Menambah Mahasiswa Baru)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Tambah Mahasiswa" | Menampilkan Form Tambah Mahasiswa baru |
| 2 | Kaprodi mengisi data mahasiswa (NIM, Nama, Email, Password, Angkatan, dll.) dan klik "Simpan" | Memvalidasi input data form (NIM/Email harus unik & terisi) |
| 3 | - | Validasi Sukses: Membuat record user baru di database dan memberikan role 'mahasiswa' |
| 4 | - | Mengalihkan kembali ke daftar dengan pesan sukses *"Mahasiswa berhasil ditambahkan"* |

---

## 3. Skenario C: Update (Mengubah Data Mahasiswa)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih mahasiswa tertentu di tabel daftar lalu klik "Edit" | Menampilkan Form Edit Mahasiswa berisi data mahasiswa terkait |
| 2 | Kaprodi memperbarui kolom data (misal: angkatan / nomor HP) dan menekan tombol "Perbarui" | Memvalidasi data yang diubah |
| 3 | - | Validasi Sukses: Menyimpan perubahan data ke record user di Database |
| 4 | - | Mengalihkan kembali ke daftar dengan pesan sukses *"Mahasiswa berhasil diperbarui"* |

---

## 4. Skenario D: Import Mahasiswa (Impor Massal Excel/CSV)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Import Mahasiswa" dan memilih file Excel/CSV, lalu klik "Unggah" | Menerima berkas file excel dan memicu parser `Excel::import(new UserImport('mahasiswa'))` |
| 2 | - | Mengurai baris file satu per satu, memvalidasi integritas data, lalu membuat user masal |
| 3 | - | Mengalihkan halaman kembali dengan notifikasi sukses *"Data mahasiswa berhasil diimpor"* |

---

## 5. Skenario E: Export Mahasiswa (Ekspor ke Excel)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Export Excel" | Mengirimkan request ekspor ke `StudentController->export()` |
| 2 | - | Query data mahasiswa prodi bersangkutan, memformat data ke format spreadsheet |
| 3 | - | Menghasilkan berkas `.xlsx` dan mengirimkan respon download otomatis ke browser Kaprodi |
