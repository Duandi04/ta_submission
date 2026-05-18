# Activity Diagram - Kelola Detail Pengajuan

Diagram ini menggambarkan alur aktivitas saat Mahasiswa mengelola atau memperbarui data dan file proposal tugas akhir mereka, terbagi dalam dua Swimlane: **USER (Mahasiswa)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Mahasiswa]
        G2[Membuka Menu Detail Pengajuan]
        G4[Mengubah Data Pengajuan <br/> & Unggah File Proposal Baru]
        G5[Klik Simpan Perubahan]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> G1[Menampilkan Halaman Dashboard Mahasiswa]
        G1 --> G2
        G2 --> G3[Menampilkan Detail Pengajuan & Tombol Edit]
        G3 --> G4
        G4 --> G5
        G5 --> G6[Validasi Data & Ekstensi File]
        G6 --> Decision{Apakah Valid?}
        Decision -- Tidak Valid --> G7[Menampilkan Pesan Validasi Error]
        G7 --> G4
        Decision -- Valid --> G8[Menyimpan Perubahan Detail & <br/> Mengunggah File Baru]
        G8 --> G9[Menampilkan Pesan Sukses <br/> & Halaman Detail Terupdate]
        G9 --> End(((⦿)))
    end
```
