# Activity Diagram - Pengaturan TA

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) melihat dan memperbarui parameter/pengaturan tugas akhir (maksimal batch pengajuan, maksimal pengajuan per batch), terbagi dalam dua Swimlane: **KAPRODI** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph KAPRODI [KAPRODI]
        L2["Mengubah pengaturan batch sistem dan pengajuan per batch"]
        L3["Klik menyimpan konfigurasi"]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> L1["Menampilkan Halaman Pengaturan sistem"]
        L4["Menyimpan perubahan pengaturan sistem"] --> End(((⦿)))
    end

    L1 --> L2
    L2 --> L3
    L3 --> L4
```
