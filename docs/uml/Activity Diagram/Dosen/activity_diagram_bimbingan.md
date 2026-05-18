# Activity Diagram - Tinjauan Bimbingan

Diagram ini menggambarkan alur aktivitas saat Dosen Pembimbing memeriksa berkas bimbingan mahasiswa dan memberikan persetujuan atau catatan revisi, terbagi dalam dua Swimlane: **USER (Dosen)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen Pembimbing]
        A2[Melihat Daftar Pengajuan Bimbingan Baru]
        A3[Memilih Pengajuan & Mengunduh Dokumen]
        A4{Apakah Dokumen Layak?}
        
        %% Revisi Flow
        A_R1[Memberikan Komentar & Rekomendasi]
        A_R2[Klik Minta Revisi]
        
        %% Setuju Flow
        A_S1[Klik Setujui Proposal]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> A1[Menampilkan Halaman Bimbingan Dosen]
        A1 --> A2
        A2 --> A3
        A3 --> A4
        
        %% Revisi Processing
        A4 -- Perlu Revisi --> A_R1
        A_R1 --> A_R2
        A_R2 --> A_R3[Update status = 'Revision Required']
        A_R3 --> A_R4[Kirim Notifikasi Revisi ke Mahasiswa]
        
        %% Setuju Processing
        A4 -- Layak --> A_S1
        A_S1 --> A_S2[Update status = 'Approved']
        A_S2 --> A_S3[Kirim Notifikasi Persetujuan ke Mahasiswa & Kaprodi]
        
        %% Unified End
        A_R4 --> End(((⦿)))
        A_S3 --> End
    end
```
