# Activity Diagram - Persiapan dan Penjadwalan Sidang

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mempersiapkan dan menjadwalkan sidang ujian untuk pengajuan tugas akhir mahasiswa yang telah disetujui, terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        C2[Filter Daftar Pengajuan Status 'Approved']
        C3[Memilih Pengajuan Mahasiswa]
        C4[Menugaskan Dosen Penguji]
        C5[Menentukan Waktu & Ruang Sidang]
        C6[Klik Jadwalkan Sidang]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> C1[Menampilkan Halaman Kelola Pengajuan]
        C1 --> C2
        C2 --> C3
        C3 --> C4
        C4 --> C5
        C5 --> C6
        C6 --> C7[Validasi Data Penjadwalan]
        C7 --> Decision{Apakah Valid?}
        Decision -- Tidak Valid --> C_Err[Menampilkan Validasi Error] --> C4
        Decision -- Valid --> C8[Update Status = 'Scheduled for Defense']
        C8 --> C9[Kirim Notifikasi Jadwal ke Dosen Penguji & Mahasiswa]
        C9 --> End(((⦿)))
    end
```
