# Fitur & Dokumentasi Kaprodi

Dokumen ini menjelaskan fungsionalitas khusus yang tersedia untuk peran **Kaprodi** (Ketua Program Studi) dalam sistem Manajemen Tugas Akhir.

---

## 1. Manajemen Mahasiswa (Kelola Mahasiswa)

Kaprodi memiliki otoritas penuh untuk mengelola data mahasiswa dalam program studi mereka:

- **Daftar Mahasiswa**: Melihat metrics "Total Pengajuan" dan "Status Proposal" (beserta Dosen Pembimbing) secara real-time.
- **Filter & Search**: Pencarian cepat berdasarkan Nama, NIM, atau Email, serta filter berdasarkan Tahun Angkatan.
- **Urutan (Sorting)**: Tabel dapat diurutkan berdasarkan NIM atau Nama secara ascending/descending.

## 2. Fitur Pengecualian Khusus (Special Case)

Fitur unik yang memungkinkan Kaprodi memberikan izin khusus kepada individu:

- **Bypass Limit**: Mahasiswa dengan label **"Pengecualian"** dapat mengunggah draft proposal lebih dari batas maksimal reguler prodi (default: 3).
- **Indikator UI**: Mahasiswa tersebut ditandai dengan badge khusus di dashboard Kaprodi dan mendapatkan notifikasi di dashboard mereka sendiri.

## 3. Sistem Laporan & Cetak

Menu khusus untuk menghasilkan laporan administratif:

- **Preview Laporan**: Menampilkan tabel mahasiswa dengan judul TA yang telah diterima dan nama dosen pembimbingnya.
- **Sorting NIM**: Laporan secara otomatis diurutkan berdasarkan NIM untuk memudahkan administrasi.
- **Export PDF**: Fitur cetak langsung ke format PDF dengan layout yang rapi dan profesional.

## 4. Keamanan & Privasi

- **Scope Prodi**: Kaprodi hanya dapat melihat dan mengelola data mahasiswa yang berada dalam Program Studi yang sama dengan akun mereka.
- **Audit Trail**: Setiap perubahan data mahasiswa (edit, delete, reset password) dicatat dalam log aktivitas sistem untuk audit di masa mendatang.
