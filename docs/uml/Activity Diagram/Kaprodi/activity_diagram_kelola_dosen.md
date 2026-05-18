# Activity Diagram - Kelola Dosen

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mengelola data dosen, terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**. Diagram ini memuat alur lengkap dari pengklikan tombol tindakan oleh user, reaksi sistem, pengisian form, validasi, hingga penyimpanan data.

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        H2[Membuka Menu Kelola Dosen]
        H4{Memilih Tindakan}
        
        %% Create
        H_C_Btn[Klik Tombol Tambah Dosen]
        H_C1[Mengisi Form Dosen Baru]
        H_C2[Klik Simpan]
        
        %% Update
        H_U_Btn[Klik Tombol Edit Dosen]
        H_U1[Mengubah Data Dosen]
        H_U2[Klik Perbarui]
        
        %% Import
        H_I_Btn[Klik Tombol Import Dosen]
        H_I1[Mengunggah File Excel/CSV]
        H_I2[Klik Import]
        
        %% Export
        H_E1[Klik Tombol Export Dosen]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> H1[Menampilkan Halaman Dashboard Kaprodi]
        H1 --> H2
        H2 --> H3[Query Dosen & Tampilkan Daftar Dosen]
        H3 --> H4
        
        %% Create Flow
        H4 -- Tambah Dosen --> H_C_Btn
        H_C_Btn --> H_C_Page[Menampilkan Form Tambah Dosen]
        H_C_Page --> H_C1
        H_C1 --> H_C2
        H_C2 --> H_C3[Validasi Form Dosen Baru]
        H_C3 --> Dec_C{Apakah Valid?}
        Dec_C -- Tidak Valid --> H_C_Err[Tampilkan Error Form] --> H_C1
        Dec_C -- Valid --> H_C4[User::create & Assign Role 'dosen']
        H_C4 --> H_C_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Update Flow
        H4 -- Edit Dosen --> H_U_Btn
        H_U_Btn --> H_U_Page[Menampilkan Form Edit Dosen]
        H_U_Page --> H_U1
        H_U1 --> H_U2
        H_U2 --> H_U3[Validasi Data Perubahan]
        H_U3 --> Dec_U{Apakah Valid?}
        Dec_U -- Tidak Valid --> H_U_Err[Tampilkan Error Form] --> H_U1
        Dec_U -- Valid --> H_U4[Update Data Dosen di Database]
        H_U4 --> H_U_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Import Flow
        H4 -- Import Massal --> H_I_Btn
        H_I_Btn --> H_I_Page[Menampilkan Modal / Form Import]
        H_I_Page --> H_I1
        H_I1 --> H_I2
        H_I2 --> H_I3[Validasi File Excel/CSV]
        H_I3 --> Dec_I{Apakah File Valid?}
        Dec_I -- Tidak Valid --> H_I_Err[Tampilkan Pesan Error Impor] --> H_I1
        Dec_I -- Valid --> H_I4[Simpan Massal Data Dosen]
        H_I4 --> H_I_End[Tampilkan Daftar Terupdate & Pesan Sukses]
        
        %% Export Flow
        H4 -- Export Excel --> H_E1
        H_E1 --> H_E2[Generate Excel Data Dosen via UserExport]
        H_E2 --> H_E3[Memicu Unduhan File Otomatis di Browser]
        
        %% Unified End
        H_C_End --> End(((⦿)))
        H_U_End --> End
        H_I_End --> End
        H_E3 --> End
    end
```
