# Activity Diagram - Tinjauan Bimbingan (Dosen Pembimbing)

Proses Dosen mengecek dokumen dari Mahasiswa bimbingannya.

```mermaid
flowchart TD
    Start[Mulai] --> A[Login sebagai Dosen Pembimbing]
    A --> B[Melihat Daftar Pengajuan Bimbingan Baru]
    B --> C[Memilih Pengajuan - Status: Submitted]
    C --> D[Mengunduh dan Memeriksa File Dokumen]
    D --> E{Apakah Konten TA Sudah Cukup Baik?}
    
    E -- Perlu Perbaikan --> F[Memberikan Komentar & Rekomendasi]
    F --> G[Update Status ke Revision Required (Revisi)]
    G --> H[Notifikasi ke Mahasiswa]
    
    E -- Sudah Layak --> I[Update Status ke Approved (Disetujui)]
    I --> J[Notifikasi ke Kaprodi & Mahasiswa]
    
    H --> K[Mahasiswa Mengunggah Revisi (Lihat Aktivitas Pengajuan)]
    J --> L[Selesai (Menunggu Jadwal Sidang)]
```
