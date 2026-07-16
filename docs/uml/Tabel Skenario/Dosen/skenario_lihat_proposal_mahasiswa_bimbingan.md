# Tabel Skenario - Lihat Proposal Mahasiswa Bimbingan

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Lihat Proposal Mahasiswa Bimbingan |
| **Aktor** | Dosen Pembimbing |
| **Deskripsi** | Menggambarkan proses Dosen Pembimbing melihat daftar pengajuan proposal tugas akhir dari mahasiswa bimbingannya |
| **Kondisi Awal** | Dosen telah masuk ke sistem dan berada di Dashboard Dosen |
| **Kondisi Akhir** | Dosen melihat daftar komprehensif mahasiswa bimbingan beserta file proposalnya |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Dosen memilih menu "Mahasiswa Bimbingan" | Mengirim permintaan data mahasiswa bimbingan |
| 2 | - | Melakukan query ke database `ThesisSubmission` untuk memfilter pengajuan berdasarkan `supervisor_id = dosen_id` |
| 3 | - | Menampilkan Halaman Bimbingan berisi daftar mahasiswa bimbingan, judul proposal, status, dan link file proposal |
