# Activity Diagram - Lihat Proposal Mahasiswa Bimbingan

Diagram ini menggambarkan alur aktivitas saat Dosen Pembimbing melihat daftar pengajuan proposal tugas akhir dari mahasiswa bimbingannya, terbagi dalam dua Swimlane: **USER (Dosen)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen]
        E2[Klik Menu Bimbingan Mahasiswa]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> E1[Menampilkan Halaman Dashboard Dosen]
        E1 --> E2
        E2 --> E3[Query Data Proposal Mahasiswa Bimbingan <br/> where supervisor_id = dosen_id]
        E3 --> E4[Menampilkan Daftar Proposal Mahasiswa Bimbingan]
        E4 --> End(((⦿)))
    end
```
