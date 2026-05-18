# Activity Diagram - Pengaturan TA

Diagram ini menggambarkan alur aktivitas saat Ketua Program Studi (Kaprodi) melihat dan memperbarui parameter/pengaturan tugas akhir (kuota bimbingan, batas pengajuan, format berkas, dll.), terbagi dalam dua Swimlane: **USER (Kaprodi)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Kaprodi]
        L2[Membuka Menu Pengaturan TA]
        L4[Mengubah Nilai Konfigurasi TA]
        L5[Klik Simpan Pengaturan]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> L1[Menampilkan Halaman Dashboard Kaprodi]
        L1 --> L2
        L2 --> L3[Query & Tampilkan Form Pengaturan TA dengan Nilai Saat Ini]
        L3 --> L4
        L4 --> L5
        L5 --> L6[Validasi Form Pengaturan]
        L6 --> Decision{Apakah Valid?}
        Decision -- Tidak Valid --> L7[Menampilkan Pesan Validasi Error]
        L7 --> L4
        Decision -- Valid --> L8[Memperbarui Nilai Kunci Pengaturan di Database]
        L8 --> L9[Menampilkan Pesan Sukses & Form Terupdate]
        L9 --> End(((⦿)))
    end
```
