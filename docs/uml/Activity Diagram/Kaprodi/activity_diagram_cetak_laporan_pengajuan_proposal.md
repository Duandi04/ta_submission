# Activity Diagram - Cetak Laporan Pengajuan Proposal

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mencetak laporan komprehensif pengajuan proposal tugas akhir mahasiswa bimbingan yang telah disetujui (Approved) secara hardcopy atau PDF, terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        M2[Membuka Menu Laporan Pengajuan]
        M4[Klik Tombol Cetak Laporan]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> M1[Menampilkan Halaman Dashboard Kaprodi]
        M1 --> M2
        M2 --> M3[Query Laporan & Tampilkan Halaman Laporan Pengajuan]
        M3 --> M4
        M4 --> M5[Tampilkan Halaman Cetak (Print View Layout)]
        M5 --> M6[Memicu Dialog window.print() Bawaan Browser]
        M6 --> End(((⦿)))
    end
```
