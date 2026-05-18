# Activity Diagram - Pengajuan Tugas Akhir (Mahasiswa)

Diagram ini menggambarkan alur aktivitas saat Mahasiswa mengisi form dan mengunggah dokumen pengajuan proposal tugas akhir baru, terbagi dalam dua Swimlane: **USER (Mahasiswa)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Mahasiswa]
        B2[Membuka Halaman Pengajuan TA]
        B3[Mengisi Form Judul & Abstrak]
        B4[Memilih Calon Dosen Pembimbing]
        B5[Mengunggah File Proposal]
        B6{Simpan sebagai Draft?}
        
        %% Draft Flow
        B_D1[Klik Simpan Draft]
        
        %% Submit Flow
        B_S1[Klik Kirim Pengajuan]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> B1[Menampilkan Halaman Dashboard Mahasiswa]
        B1 --> B2
        B2 --> B3
        B3 --> B4
        B4 --> B5
        B5 --> B6
        
        %% Draft Save
        B6 -- Ya --> B_D1
        B_D1 --> B_D2[Validasi & Simpan Status 'Draft']
        B_D2 --> B_D_End[Tampilkan Dashboard dengan Status Draft]
        
        %% Submit Processing
        B6 -- Tidak --> B_S1
        B_S1 --> B_S2[Validasi & Simpan Status 'Submitted']
        B_S2 --> B_S3[Kirim Notifikasi Pengajuan ke Dosen Pembimbing]
        B_S3 --> B_S_End[Tampilkan Dashboard dengan Status Submitted]
        
        %% Unified End
        B_D_End --> End(((⦿)))
        B_S_End --> End
    end
```
