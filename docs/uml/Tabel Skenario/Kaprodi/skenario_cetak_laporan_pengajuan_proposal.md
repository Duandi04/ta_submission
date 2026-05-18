# Tabel Skenario - Cetak Laporan Pengajuan Proposal

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Cetak Laporan Pengajuan Proposal |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi menarik dan mencetak laporan daftar mahasiswa bimbingan yang telah disetujui proposalnya beserta dosen pembimbingnya |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Laporan ditampilkan dalam layout ramah cetak dan memicu dialog pencetakan browser |

## Alur Utama (Basic Flow)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih menu "Laporan Pengajuan" | Mengirimkan request ke `KaprodiController->reportIndex()` |
| 2 | - | Query data mahasiswa ber-role 'mahasiswa' yang memiliki status proposal disetujui (`'approved'`) beserta relasi dosen pembimbingnya |
| 3 | - | Menampilkan Halaman Rekap Laporan Pengajuan Proposal lengkap dengan tombol "Cetak Laporan" |
| 4 | Kaprodi mengklik tombol "Cetak Laporan" | Mengirimkan request cetak ke `KaprodiController->reportPrint()` |
| 5 | - | Menampilkan halaman dengan layout khusus cetak (*print stylesheet friendly*, tanpa menu samping/header dashboard) |
| 6 | - | Memicu eksekusi JavaScript `window.print()` untuk memunculkan dialog cetak bawaan browser |
| 7 | Kaprodi memilih opsi printer (atau Simpan sebagai PDF) di dialog browser dan mengklik "Print / Save" | Dialog tertutup, browser mengunduh berkas PDF laporan atau mencetaknya ke kertas |
