# Activity Diagram - Kelola Detail Pengajuan

Diagram ini menggambarkan alur aktivitas saat Mahasiswa mengelola atau memperbarui data dan file proposal tugas akhir mereka (baik membuat pengajuan baru atau mengelola draft detail dengan opsi ajukan/batalkan), terbagi dalam dua Swimlane: **USER (Mahasiswa)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Mahasiswa]
        G2["Klik Menu Daftar Pengajuan"]
        
        %% Choice: Create new proposal or manage existing draft
        G5{"Pilih Tindakan"}
        
        %% Path A: Create
        G5_Create["Klik 'Buat Pengajuan Baru'"]
        G6_Create["Mengisi Form & Klik 'Kirim'"]
        
        %% Path B: Manage Draft
        G5_Draft["Klik 'Detail' pada Pengajuan Berstatus Draft"]
        G6_Draft{"Pilih Sub-Tindakan"}
        G7_Draft_Submit["Klik 'Ajukan Sekarang'"]
        G7_Draft_Cancel["Klik 'Batalkan Pengajuan'"]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> G1["Menampilkan Dashboard Mahasiswa"]
        G3["Query & Menampilkan Daftar Pengajuan"]
        
        %% System Path A
        G4_Create["Menampilkan Form Pengajuan Baru"]
        G8_Create["Validasi Data & Ekstensi File"]
        G8_Create_Check{Apakah Valid?}
        G8_Create_Error["Tampilkan Pesan Validasi Error"]
        G9_Create["Simpan Proposal Baru (Status Draft) & File ke Storage"]
        
        %% System Path B
        G4_Draft["Menampilkan Halaman Detail Pengajuan Draft"]
        
        %% Sub System Draft Actions
        G8_Draft_Submit["Mengubah Status Proposal menjadi 'submitted'"]
        G8_Draft_Cancel["Menghapus File dari Storage & Hapus Baris Pengajuan dari Database"]
        
        G_Final["Redirect ke Daftar Pengajuan dengan Alert Notifikasi"]
    end

    G1 --> G2
    G2 --> G3
    G3 --> G5
    
    %% Path A connection
    G5 -->|Buat Pengajuan| G5_Create
    G5_Create --> G4_Create
    G4_Create --> G6_Create
    G6_Create --> G8_Create
    G8_Create --> G8_Create_Check
    G8_Create_Check -->|Tidak Valid| G8_Create_Error
    G8_Create_Error --> G6_Create
    G8_Create_Check -->|Valid| G9_Create
    G9_Create --> G_Final
    
    %% Path B connection
    G5 -->|Kelola Draft| G5_Draft
    G5_Draft --> G4_Draft
    G4_Draft --> G6_Draft
    G6_Draft -->|Ajukan| G7_Draft_Submit
    G6_Draft -->|Batalkan| G7_Draft_Cancel
    
    G7_Draft_Submit --> G8_Draft_Submit
    G7_Draft_Cancel --> G8_Draft_Cancel
    
    G8_Draft_Submit --> G_Final
    G8_Draft_Cancel --> G_Final
    G_Final --> End(((⦿)))
```
