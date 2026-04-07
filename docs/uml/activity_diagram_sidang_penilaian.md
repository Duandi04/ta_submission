# Activity Diagram - Pelaksanaan Sidang dan Penilaian (Dosen Penguji)

Proses yang terjadi saat sidang dilangsungkan hingga pemberian nilai akhir oleh dosen penguji.

```mermaid
flowchart TD
    Start[Sidang Berlangsung] --> A[Login sebagai Dosen Penguji]
    A --> B[Pilih Menu Penilaian pada Mahasiswa Terjadwal]
    B --> C[Memasukkan Poin Penilaian ke dalam Form Asesmen / Rubrik]
    C --> D[Memberikan Catatan Kekuatan, Kelemahan, Saran]
    D --> E[Simpan Draft Penilaian atau Submit Final?]
    
    E -- Draft --> F[Berdasarkan Kebutuhan Diskusi Sidang]
    F --> C
    E -- Submit Final --> G[Mengunci Nilai Ujian]
    
    G --> H[Sistem Menghitung Total Nilai (Assessment total_score)]
    H --> I{Apakah Semua Dosen Penguji Telah Menilai?}
    
    I -- Belum --> J[Menunggu Penguji Lain]
    I -- Sudah --> K[Sistem Merekap Nilai Akhir]
    K --> L{Apakah Hasil Sidang Lulus?}
    
    L -- Lulus Sempurna --> M[Status berubah ke Completed]
    L -- Lulus Bersyarat (Revisi) --> N[Status berubah ke Revision Required]
    L -- Tidak Lulus --> O[Status berubah ke Rejected]
    
    M --> End[Selesai]
    N --> End
    O --> End
```
