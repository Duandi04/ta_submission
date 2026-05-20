# Activity Diagram - Menilai Draft Proposal Mahasiswa

Diagram ini menggambarkan alur aktivitas saat Dosen memberikan penilaian (skor kriteria rubrik & komentar) terhadap proposal tugas akhir mahasiswa, terbagi dalam dua Swimlane: **USER (Dosen)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen]
        F2["Membuka Daftar Review & Klik 'Edit/Nilai' pada Proposal"]
        F4["Mengisi Skor Rubrik Penilaian & Komentar Kelayakan"]
        
        %% Deciding to Draft or Submit Final
        F5{"Pilih Tindakan"}
        F5_Draft["Klik 'Perbarui Draft Penilaian'"]
        F5_Final["Klik 'Kirim Penilaian Final'"]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> F1["Menampilkan Dashboard Dosen"]
        F3["Query Data Proposal & Rubrik -> Tampilkan Form Penilaian"]
        
        %% Handling Draft Save
        F6_Draft["Menyimpan Penilaian sebagai Draft (Status Tetap Draft)"]
        F7_Draft["Menampilkan Alert 'Draft Berhasil Diperbarui' & Form Nilai"]
        
        %% Handling Final Submit
        F6_Final["Memvalidasi Input Penilaian secara Lengkap"]
        F6_Final_Check{Apakah Valid?}
        F6_Error["Tampilkan Pesan Validasi Error"]
        F7_Final["Simpan Penilaian Permanen & Ubah Status ThesisSubmission menjadi 'sudah submit'"]
        F8_Final["Redirect Back ke Daftar Review dengan Alert Sukses"]
    end

    F1 --> F2
    F2 --> F3
    F3 --> F4
    F4 --> F5
    
    %% Path A: Draft Save
    F5 -->|Simpan Sementara| F5_Draft
    F5_Draft --> F6_Draft
    F6_Draft --> F7_Draft
    F7_Draft --> F4
    
    %% Path B: Final Submit
    F5 -->|Kirim Final| F5_Final
    F5_Final --> F6_Final
    F6_Final --> F6_Final_Check
    F6_Final_Check -->|Tidak Valid| F6_Error
    F6_Error --> F4
    F6_Final_Check -->|Valid| F7_Final
    F7_Final --> F8_Final
    F8_Final --> End(((⦿)))
```
