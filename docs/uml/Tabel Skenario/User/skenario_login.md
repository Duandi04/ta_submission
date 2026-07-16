# Tabel Skenario - Login

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Login |
| **Aktor** | User (Umum: Mahasiswa / Dosen / Kaprodi) |
| **Deskripsi** | Menggambarkan proses otentikasi pengguna ke dalam sistem |
| **Kondisi Awal** | Pengguna berada di Halaman Login dan belum masuk sistem |
| **Kondisi Akhir** | Pengguna masuk ke halaman Dashboard utama sesuai dengan peran masing-masing |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Pengguna mengakses Halaman Login | Menampilkan Form Login (input Email dan Password) |
| 2 | Pengguna memasukkan Email dan Password, lalu menekan tombol "Login" | Menerima data dan melakukan validasi kredensial ke Database |
| 3 | - | Otentikasi sukses: Membuat session baru |
| 4 | - | Mengarahkan pengguna dan menampilkan Halaman Dashboard utama |

## Alur Alternatif / Eksepsi (Alternative Flow)

* **2a. Validasi Kredensial Gagal (Email / Password Salah):**
  1. Sistem mendeteksi data yang dimasukkan tidak cocok dengan database.
  2. Sistem menampilkan pesan error *"Kredensial tidak cocok dengan data kami"* di atas form login.
  3. Sistem tetap berada di Halaman Login.
