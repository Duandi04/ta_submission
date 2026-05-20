# Activity Diagram - Pengaturan TA per Program Studi

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) melihat dan memperbarui parameter/pengaturan tugas akhir (rentang tanggal deadline pengajuan, maksimal batch, kuota attempts pengajuan per batch), terbagi dalam dua Swimlane: **KAPRODI** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph KAPRODI [KAPRODI]
        L2["Mengisi Waktu Mulai & Deadline Pengajuan, Maksimal Batch, dan Kuota Percobaan"]
        L3["Klik menyimpan konfigurasi"]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> L1["Menampilkan Halaman Pengaturan Program Studi & Input Deadline"]
        L4["Memvalidasi input & menyimpan data ke tabel program_studis"] --> End(((⦿)))
    end

    L1 --> L2
    L2 --> L3
    L3 --> L4
```
