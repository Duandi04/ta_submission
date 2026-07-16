# Activity Diagram - Ganti Password

Diagram ini menggambarkan alur aktivitas proses penggantian kata sandi (password) pengguna ke dalam sistem, yang terbagi dalam dua Swimlane: **USER** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER]
        C2[Klik Profile]
        C4[Klik Profile Dropdown]
        C6[Mengisi Form Ganti Password]
        C7[Klik Simpan Perubahan]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> C1[Menampilkan Halaman Dashboard]
        C1 --> C2
        C2 --> C3[Menampilkan Menu Dropdown <br/> Profile & Logout]
        C3 --> C4
        C4 --> C5[Menampilkan Halaman Edit Profile <br/> Tab Ganti Password]
        C5 --> C6
        C6 --> C7
        C7 --> C8[Validasi Password Lama & Konfirmasi]
        C8 --> Decision{Apakah Valid?}
        Decision -- Tidak Valid --> C9[Menampilkan Pesan Error <br/> & Tetap di Halaman]
        C9 --> C6
        Decision -- Valid --> C10[Menyimpan Password Baru & <br/> Menampilkan Pesan Sukses]
        C10 --> End(((⦿)))
    end
```
