# Activity Diagram - Kelola Rubrik Penilaian

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mengelola rubrik dan kriteria penilaian tugas akhir, terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**. Diagram mencakup alur melihat daftar rubrik (Read), menambah rubrik (Create), mengubah rubrik (Update), dan menghapus rubrik (Delete).

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        K2[Membuka Menu Kelola Rubrik]
        K4{Memilih Tindakan}
        
        %% Create Flow
        K_C1[Mengisi Detail Rubrik & Kriteria Baru]
        K_C2[Klik Simpan Rubrik]
        
        %% Update Flow
        K_U1[Mengubah Data Rubrik / Kriteria]
        K_U2[Klik Perbarui Rubrik]
        
        %% Delete Flow
        K_D1[Klik Tombol Hapus Rubrik]
        K_D2[Konfirmasi Hapus]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> K1[Menampilkan Halaman Dashboard Kaprodi]
        K1 --> K2
        K2 --> K3[Query & Tampilkan Halaman Daftar Rubrik Penilaian]
        K3 --> K4
        
        %% Create Processing
        K4 -- Tambah Rubrik --> K_C1
        K_C1 --> K_C2
        K_C2 --> K_C3[Validasi Form Rubrik & Kriteria]
        K_C3 --> Dec_C{Apakah Valid?}
        Dec_C -- Tidak Valid --> K_C_Err[Tampilkan Validasi Error] --> K_C1
        Dec_C -- Valid --> K_C4[Rubric::create & Simpan AssessmentCriterion]
        K_C4 --> K_C_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Update Processing
        K4 -- Edit Rubrik --> K_U1
        K_U1 --> K_U2
        K_U2 --> K_U3[Validasi Form Perubahan Rubrik]
        K_U3 --> Dec_U{Apakah Valid?}
        Dec_U -- Tidak Valid --> K_U_Err[Tampilkan Validasi Error] --> K_U1
        Dec_U -- Valid --> K_U4[Update Data Rubrik & Sync Kriteria]
        K_U4 --> K_U_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Delete Processing
        K4 -- Hapus Rubrik --> K_D1
        K_D1 --> K_D2
        K_D2 --> K_D3[Hapus Record Rubrik & Kriteria Terkait]
        K_D3 --> K_D_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Unified End
        K_C_End --> End(((⦿)))
        K_U_End --> End
        K_D_End --> End
    end
```
