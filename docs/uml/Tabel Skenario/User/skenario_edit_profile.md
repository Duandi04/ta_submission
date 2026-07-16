# Tabel Skenario - Edit Profile

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Edit Profile |
| **Aktor** | User (Umum) |
| **Deskripsi** | Menggambarkan proses pembaruan informasi profil pengguna (nama, email, telepon, alamat, foto profil) |
| **Kondisi Awal** | Pengguna telah masuk ke sistem dan berada di halaman Dashboard |
| **Kondisi Akhir** | Informasi profil pengguna diperbarui di database dan ditampilkan secara terupdate |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Pengguna memilih menu/dropdown "Profile" | Menampilkan Halaman Edit Profile dengan data saat ini terisi otomatis di form |
| 2 | Pengguna memperbarui data pada form (misal: mengubah nama/nomor telepon) dan mengklik "Simpan Perubahan" | Menerima data dan memvalidasi format input (seperti keunikan email) |
| 3 | - | Validasi sukses: Menyimpan perubahan data profil dan mengunggah foto baru jika ada |
| 4 | - | Mengalihkan kembali ke Halaman Profil dengan pesan sukses *"Profil berhasil diperbarui"* |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **2a. Validasi Form Gagal (Format Salah / Email Duplikat):**
  1. Sistem mendeteksi email yang dimasukkan sudah digunakan pengguna lain atau format data tidak valid.
  2. Sistem membatalkan penyimpanan.
  3. Sistem menampilkan pesan error validasi spesifik di dekat kolom input yang bermasalah.
