# Activity Diagram - Kelola Daftar Pengajuan

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) mengelola daftar pengajuan proposal tugas akhir mahasiswa. Penentuan tombol tindakan yang dapat diakses oleh Kaprodi dikontrol secara dinamis oleh sistem berdasarkan kondisi data dan status pengajuan aktual, khususnya pengecekan kelengkapan nilai saat status pengajuan berada di fase review (`under_review`).

```mermaid
flowchart TD
    subgraph USER [KAPRODI]
        %% Top Branching Choice
        U_Choice{Pilihan Tindakan}
        
        %% Tambah History Flow
        U_H1(Klik tombol Tambah data 'history')
        U_H2(Mengisi data history pengajuan)
        U_H3(Klik Simpan data history)
        
        %% Kelola Pengajuan Flow
        U_DetHist(Klik Edit pada pengajuan history)
        U_Det1(Klik Detail pada pengajuan dengan status 'sudah diajukan')
        U_Det2(Klik Detail pada pengajuan dengan status 'sedang ditinjau')
        
        %% status == 'submitted' (sudah diajukan)
        U_Sub_Choice{Pilihan Aksi Kaprodi}
        U_S1(Memilih dosen penilai & rubrik penilaian yang digunakan)
        U_S2(Klik Simpan Perubahan)
        
        U_SR1(Klik Tolak)
        U_SR2(Mengisi alasan penolakan)
        U_SR3(Klik Konfirmasi Tolak)
        
        %% status == 'under_review' (sedang ditinjau) - Semua Dosen Penilai Telah Menilai
        U_UR_Choice{Pilihan Aksi Kaprodi}
        U_U1(Memilih dosen pembimbing 1 & dosen pembimbing 2)
        U_U2(Klik 'Terima Pengajuan & Tetapkan Pembimbing')
        
        U_UR_R1(Klik Tolak)
        U_UR_R2(Mengisi alasan penolakan)
        U_UR_R3(Klik Konfirmasi Tolak)
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> S_List(Menampilkan halaman Daftar Pengajuan)
        S_List --> U_Choice
        
        %% TAMBAH DATA HISTORY PATH
        U_Choice -- Tambah History --> U_H1
        U_H1 --> S_Add_Form(Menampilkan form input data 'history' pengajuan)
        S_Add_Form --> U_H2
        U_H2 --> U_H3
        U_H3 --> S_Save_History(Menyecocokkan & menyimpan data history ke dalam sistem)
        
        %% KELOLA PENGAJUAN PATH
        U_Choice -- Kelola Pengajuan --> U_Select_Sub{Cek Status & Kondisi Pengajuan}
        
        %% ==========================================
        %% FASE HISTORICAL: DATA HISTORY
        %% ==========================================
        U_Select_Sub -- Data History --> U_DetHist
        U_DetHist --> S_Edit_Hist_Page(Menampilkan Form Edit Data History Pengajuan)
        S_Edit_Hist_Page --> U_UR_R2
        
        %% ==========================================
        %% FASE 1: STATUS 'SUDAH DIAJUKAN'
        %% ==========================================
        U_Select_Sub -- Status: 'Sudah Diajukan' --> U_Det1
        U_Det1 --> S_Det_Page1(Menampilkan halaman detail pengajuan & Form Atur Dosen Penilai)
        S_Det_Page1 --> U_Sub_Choice
        
        %% Persetujuan Penilai & Rubrik
        U_Sub_Choice -- Tentukan Penilai & Rubrik --> U_S1
        U_S1 --> U_S2
        U_S2 --> S_Save_Assessors(Status otomatis berubah menjadi 'Sedang Ditinjau' & membuat data penilaian untuk dosen penilai)
        
        %% Penolakan Awal (Submitted)
        U_Sub_Choice -- Tolak Pengajuan --> U_SR1
        U_SR1 --> S_Modal_R1(Menampilkan modal pengisian alasan penolakan)
        S_Modal_R1 --> U_SR2
        U_SR2 --> U_SR3
        U_SR3 --> S_Reject1(Status pengajuan berubah menjadi ditolak 'rejected' & menyimpan alasan)
        
        %% ==========================================
        %% FASE 2: STATUS 'SEDANG DITINJAU'
        %% ==========================================
        U_Select_Sub -- Status: 'Sedang Ditinjau' --> U_Det2
        U_Det2 --> S_Det_Page2(Menampilkan halaman detail pengajuan & Cek Status Penilaian)
        
        %% Evaluasi Kelengkapan Nilai Dosen Penguji (Baris 325-327 show.blade.php)
        S_Det_Page2 --> S_Check_Assessed{Apakah semua dosen penilai telah memberikan nilai?}
        
        %% Kasus A: Belum semua dinilai
        S_Check_Assessed -- Tidak --> S_Wait(Menampilkan pesan 'Menunggu Penilaian' & Menyembunyikan form persetujuan)
        
        %% Kasus B: Sudah semua dinilai
        S_Check_Assessed -- Ya --> S_Show_Accept_Form(Menampilkan Form Atur Dosen Pembimbing)
        S_Show_Accept_Form --> U_UR_Choice
        
        %% Terima & Tetapkan Pembimbing
        U_UR_Choice -- Terima & Tetapkan Pembimbing --> U_U1
        U_U1 --> U_U2
        U_U2 --> S_Accept(Status pengajuan berubah menjadi disetujui 'approved' & menetapkan dosen pembimbing)
        
        %% Penolakan Akhir (Setelah Dinilai)
        U_UR_Choice -- Tolak Pengajuan --> U_UR_R1
        U_UR_R1 --> S_Modal_R2(Menampilkan modal pengisian alasan penolakan)
        S_Modal_R2 --> U_UR_R2
        U_UR_R2 --> U_UR_R3
        U_UR_R3 --> S_Reject2(Status pengajuan berubah menjadi ditolak 'rejected' & menyimpan alasan)
        
        %% UNIFIED END NODE (⦿)
        End([⦿])
        S_Save_History --> End
        S_Save_Assessors --> End
        S_Reject1 --> End
        S_Wait --> End
        S_Accept --> End
        S_Reject2 --> End
    end
```
