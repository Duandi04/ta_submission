# Activity Diagram - Lihat Proposal Mahasiswa Bimbingan

Diagram ini menggambarkan alur aktivitas saat Dosen Pembimbing melihat daftar pengajuan proposal tugas akhir dari mahasiswa bimbingannya, terbagi dalam dua Swimlane: **USER (Dosen)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen]
        E2["Klik Menu Bimbingan Mahasiswa"]
        E5["Klik 'Draft' pada Baris Data Mahasiswa"]
        E8["Klik 'Detail' pada Proposal Berstatus Diterima"]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> E1["Menampilkan Dashboard Dosen"]
        E3["Query Mahasiswa Bimbingan (supervisor_id = dosen_id)"]
        E4["Menampilkan Daftar Mahasiswa Bimbingan"]
        E6["Query Seluruh Draft/Proposal Milik Mahasiswa Pilihan"]
        E7["Menampilkan Daftar Proposal/Draft Mahasiswa"]
        E9["Query Detail Proposal Pilihan"]
        E10["Menampilkan Halaman Detail Proposal Bimbingan"] --> End(((⦿)))
    end

    E1 --> E2
    E2 --> E3
    E3 --> E4
    E4 --> E5
    E5 --> E6
    E6 --> E7
    E7 --> E8
    E8 --> E9
    E9 --> E10
```
