# Activity Diagram - Persiapan dan Penjadwalan Sidang (Kaprodi)

Kaprodi menindaklanjuti pengajuan yang sudah disetujui Dosen Pembimbing.

```mermaid
flowchart TD
    Start[Mulai] --> A[Login sebagai Kaprodi]
    A --> B[Lihat Daftar Pengajuan]
    B --> C[Filter Pengajuan berstatus "Approved"]
    C --> D[Menugaskan Dosen Penguji (Examiner) untuk Mahasiswa Terkait]
    D --> E[Menentukan Tanggal dan Waktu Sidang]
    E --> F[Menekan Tombol Jadwalkan Sidang]
    F --> G[Update Status ke Scheduled for Defense]
    G --> H[Notifikasi Penugasan via Sistem ke Dosen Penguji]
    H --> I[Notifikasi Jadwal ke Mahasiswa]
    I --> End[Selesai]
```
