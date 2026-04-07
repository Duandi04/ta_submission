# Activity Diagram - Pengajuan Tugas Akhir (Mahasiswa)

Aktivitas ini menunjukkan bagaimana Mahasiswa mengirimkan (membuat draft lalu submit) pengajuannya beserta dokumen file yang diperlukan.

```mermaid
flowchart TD
    Start[Mulai] --> A[Login Sistem sebagai Mahasiswa]
    A --> B[Buka Halaman Pengajuan TA]
    B --> C[Isi Form Pengajuan (Judul, Abstrak)]
    C --> D[Pilih Dosen Pembimbing (Jika Diizinkan Sistem)]
    D --> E[Unggah File Dokumen]
    E --> F{Simpan sebagai Draft?}
    
    F -- Ya --> G[Dashboard Mahasiswa - Status: Draft]
    F -- Tidak --> H[Submit Pengajuan - Status: Submitted]
    H --> I[Sistem Mengirim Notifikasi ke Dosen Pembimbing]
    
    G --> B
    I --> J[Selesai (Menunggu Review)]
```
