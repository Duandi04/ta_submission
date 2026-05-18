# Tabel Skenario - Logout

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Logout |
| **Aktor** | User (Umum) |
| **Deskripsi** | Menggambarkan proses keluar dari sistem dan membersihkan session pengguna |
| **Kondisi Awal** | Pengguna telah masuk ke sistem dan berada di salah satu halaman sistem |
| **Kondisi Akhir** | Pengguna keluar dari sistem, session dihapus, dan kembali ke Halaman Login |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Pengguna mengklik foto profil atau nama pengguna di pojok kanan atas | Menampilkan menu dropdown (Profile & Logout) |
| 2 | Pengguna mengklik tombol "Logout" | Menerima permintaan logout |
| 3 | - | Menghapus data session pengguna dan meregenerasi token csrf |
| 4 | - | Mengalihkan halaman dan menampilkan kembali Halaman Login |
