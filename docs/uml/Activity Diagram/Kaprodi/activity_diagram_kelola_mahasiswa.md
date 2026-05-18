# Activity Diagram - Kelola Mahasiswa

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mengelola data mahasiswa, terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**. Diagram ini memuat alur lengkap dari pengklikan tombol tindakan oleh user, reaksi sistem, pengisian form, validasi, hingga penyimpanan data.

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        J2[Membuka Menu Kelola Mahasiswa]
        J4{Memilih Tindakan}
        
        %% Create
        J_C_Btn[Klik Tombol Tambah Mahasiswa]
        J_C1[Mengisi Form Mahasiswa Baru]
        J_C2[Klik Simpan]
        
        %% Update
        J_U_Btn[Klik Tombol Edit Mahasiswa]
        J_U1[Mengubah Data Mahasiswa]
        J_U2[Klik Perbarui]
        
        %% Import
        J_I_Btn[Klik Tombol Import Mahasiswa]
        J_I1[Mengunggah File Excel/CSV]
        J_I2[Klik Import]
        
        %% Export
        J_E1[Klik Tombol Export Mahasiswa]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> J1[Menampilkan Halaman Dashboard Kaprodi]
        J1 --> J2
        J2 --> J3[Query Mahasiswa & Tampilkan Daftar Mahasiswa]
        J3 --> J4
        
        %% Create Flow
        J4 -- Tambah Mahasiswa --> J_C_Btn
        J_C_Btn --> J_C_Page[Menampilkan Form Tambah Mahasiswa]
        J_C_Page --> J_C1
        J_C1 --> J_C2
        J_C2 --> J_C3[Validasi Form Mahasiswa Baru]
        J_C3 --> Dec_C{Apakah Valid?}
        Dec_C -- Tidak Valid --> J_C_Err[Tampilkan Error Form] --> J_C1
        Dec_C -- Valid --> J_C4[User::create & Assign Role 'mahasiswa']
        J_C4 --> J_C_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Update Flow
        J4 -- Edit Mahasiswa --> J_U_Btn
        J_U_Btn --> J_U_Page[Menampilkan Form Edit Mahasiswa]
        J_U_Page --> J_U1
        J_U1 --> J_U2
        J_U2 --> J_U3[Validasi Data Perubahan]
        J_U3 --> Dec_U{Apakah Valid?}
        Dec_U -- Tidak Valid --> J_U_Err[Tampilkan Error Form] --> J_U1
        Dec_U -- Valid --> J_U4[Update Data Mahasiswa di Database]
        J_U4 --> J_U_End[Tampilkan Daftar & Notifikasi Sukses]
        
        %% Import Flow
        J4 -- Import Massal --> J_I_Btn
        J_I_Btn --> J_I_Page[Menampilkan Modal / Form Import]
        J_I_Page --> J_I1
        J_I1 --> J_I2
        J_I2 --> J_I3[Validasi File Excel/CSV]
        J_I3 --> Dec_I{Apakah File Valid?}
        Dec_I -- Tidak Valid --> J_I_Err[Tampilkan Pesan Error Impor] --> J_I1
        Dec_I -- Valid --> J_I4[Simpan Massal Data Mahasiswa]
        J_I4 --> J_I_End[Tampilkan Daftar Terupdate & Pesan Sukses]
        
        %% Export Flow
        J4 -- Export Excel --> J_E1
        J_E1 --> J_E2[Generate Excel Data Mahasiswa via UserExport]
        J_E2 --> J_E3[Memicu Unduhan File Otomatis di Browser]
        
        %% Unified End
        J_C_End --> End(((⦿)))
        J_U_End --> End
        J_I_End --> End
        J_E3 --> End
    end
```
