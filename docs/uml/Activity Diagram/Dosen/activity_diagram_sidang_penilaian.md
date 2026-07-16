# Activity Diagram - Pelaksanaan Sidang dan Penilaian

Diagram ini menggambarkan alur aktivitas saat Dosen Penguji melaksanakan sidang dan memberikan nilai akhir ujian tugas akhir mahasiswa, terbagi dalam dua Swimlane: **USER (Dosen Penguji)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen Penguji]
        D2[Pilih Menu Penilaian Mahasiswa Terjadwal]
        D3[Memasukkan Poin Skor Rubrik & Komentar Ujian]
        D4{Pilihan Penyimpanan?}
        
        %% Draft Flow
        D_Draft[Klik Simpan Draft]
        
        %% Final Flow
        D_Final[Klik Kirim Nilai Final]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> D1[Menampilkan Daftar Jadwal Sidang Dosen]
        D1 --> D2
        D2 --> D3
        D3 --> D4
        
        %% Draft Save
        D4 -- Draft --> D_Draft
        D_Draft --> D_D_Save[Simpan Nilai Sementara]
        D_D_Save --> D_D_End[Kembali ke Form Penilaian]
        
        %% Final Save
        D4 -- Final --> D_Final
        D_Final --> D_F_Save[Kunci Nilai Ujian & Hitung Total Nilai]
        D_F_Save --> D_F_Check{Apakah Semua Penguji Sudah Menilai?}
        D_F_Check -- Belum --> D_Wait[Menunggu Penguji Lain]
        D_F_Check -- Sudah --> D_Calc[Rekap & Hitung Nilai Akhir]
        
        D_Calc --> Dec_Result{Apakah Lulus?}
        Dec_Result -- Lulus --> D_Lulus[Update Status = 'Completed']
        Dec_Result -- Lulus Bersyarat --> D_Revisi[Update Status = 'Revision Required']
        Dec_Result -- Tidak Lulus --> D_Gagal[Update Status = 'Rejected']
        
        %% Unified End
        D_D_End --> D3
        D_Wait --> End(((⦿)))
        D_Lulus --> End
        D_Revisi --> End
        D_Gagal --> End
    end
```
