# Activity Diagram - Menilai Draft Proposal Mahasiswa

Diagram ini menggambarkan alur aktivitas saat Dosen Penguji memberikan penilaian dan komentar terhadap draft proposal tugas akhir mahasiswa bimbingan atau ujiannya, terbagi dalam dua Swimlane: **USER (Dosen)** dan **SYSTEM**.

```mermaid
flowchart TD
    subgraph USER [USER: Dosen]
        F2[Memilih Proposal Mahasiswa]
        F4[Mengisi Form Penilaian Rubrik & Komentar]
        F5[Klik Kirim Penilaian]
    end

    subgraph SYSTEM [SYSTEM]
        Start([●]) --> F1[Menampilkan Daftar Proposal Mahasiswa]
        F1 --> F2
        F2 --> F3[Menampilkan Halaman Detail Proposal & Tombol Penilaian]
        F3 --> F4
        F4 --> F5
        F5 --> F6[Validasi Nilai & Komentar]
        F6 --> Decision{Apakah Valid?}
        Decision -- Tidak Valid --> F7[Menampilkan Pesan Error <br/> & Tetap di Halaman Form]
        F7 --> F4
        Decision -- Valid --> F8[Menyimpan Penilaian & <br/> Memperbarui Status Kelayakan Proposal]
        F8 --> F9[Menampilkan Detail Proposal Terupdate <br/> & Notifikasi Sukses]
        F9 --> End(((⦿)))
    end
```
