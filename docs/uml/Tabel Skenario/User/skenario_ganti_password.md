# Tabel Skenario - Ganti Password

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Ganti Password |
| **Aktor** | User (Umum) |
| **Deskripsi** | Menggambarkan alur penggantian password pengguna untuk menjaga keamanan akun |
| **Kondisi Awal** | Pengguna telah masuk ke sistem dan berada di Halaman Edit Profile |
| **Kondisi Akhir** | Password berhasil diperbarui di database dan sistem memberikan pesan sukses |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Pengguna memilih tab "Ganti Password" | Menampilkan form isian (Password Lama, Password Baru, Konfirmasi Password Baru) |
| 2 | Pengguna mengisi form dengan lengkap dan menekan tombol "Simpan" | Menerima data dan memvalidasi password lama serta kecocokan konfirmasi |
| 3 | - | Validasi sukses: Mengenkripsi (hash) password baru dan memperbarui record di database |
| 4 | - | Menampilkan notifikasi sukses *"Password berhasil diperbarui"* |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **2a. Password Lama Salah:**
  1. Sistem mendeteksi password lama yang diinputkan tidak cocok dengan hash di database.
  2. Sistem menampilkan pesan error *"Password saat ini salah"*.
  3. Tetap berada di halaman ganti password dengan form dikosongkan.

* **2b. Konfirmasi Password Baru Tidak Cocok:**
  1. Sistem mendeteksi password baru dan konfirmasi password tidak sama.
  2. Sistem menampilkan pesan error *"Konfirmasi password baru tidak cocok"*.
