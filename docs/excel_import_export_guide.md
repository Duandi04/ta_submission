# Dokumentasi Fitur Import/Export Excel

Fitur ini memungkinkan Admin dan Kaprodi untuk mengelola data pengguna (Mahasiswa dan Dosen) secara massal menggunakan file Excel (.xlsx atau .xls).

## Fitur Export

Tombol **Export** akan mengunduh data pengguna yang sedang ditampilkan di tabel ke dalam format Excel.

- **Admin**: Dapat mengekspor semua pengguna atau berdasarkan filter (Role, Program Studi, Pencarian).
- **Kaprodi**: Dapat mengekspor daftar Mahasiswa atau Dosen di Program Studi masing-masing.

## Fitur Import

Tombol **Import** memungkinkan pengunggahan data baru atau pembaruan data yang sudah ada secara massal.

### Aturan Import

1. **Mencegah Duplikasi**: Sistem secara otomatis mengecek apakah pengguna sudah ada berdasarkan **Email** atau **NIM/NIP**.
    - Jika ditemukan, data pengguna tersebut akan **diperbarui** (Update).
    - Jika tidak ditemukan, data akan **ditambahkan** sebagai pengguna baru (Create).
2. **Password**:
    - Jika kolom password diisi, sistem akan mengenkripsi dan menggunakannya.
    - Jika dikosongkan untuk pengguna baru, password default adalah `password123`.
    - Jika dikosongkan untuk pengguna lama, password tidak akan berubah.
3. **Role**:
    - **Admin Import**: Harus mengisi kolom `role` (contoh: mahasiswa, dosen, kaprodi). Sistem akan menolak jika role tidak valid.
    - **Kaprodi Import**: Role otomatis ditentukan berdasarkan halaman (Mahasiswa otomatis jadi `mahasiswa`, Dosen otomatis jadi `dosen`). Kolom `role` di Excel akan diabaikan.
4. **Program Studi**: Isi kolom `program_studi` dengan nama lengkap program studi (contoh: Teknik Informatika). Sistem akan mencoba mencocokkan nama tersebut dengan database.

### Format Kolom Excel

File Excel harus memiliki header (baris pertama) dengan nama kolom berikut (huruf kecil):

| Kolom | Deskripsi | Wajib |
| :--- | :--- | :--- |
| `nama` | Nama lengkap pengguna | Ya |
| `email` | Alamat email unik | Ya |
| `nim_nip` | Nomor Induk (NIM untuk Mahasiswa, NIP untuk Dosen) | Ya (untuk identifikasi) |
| `password` | Kata sandi (minimal 8 karakter) | Tidak |
| `telepon` | Nomor telepon/WhatsApp | Tidak |
| `alamat` | Alamat tempat tinggal | Tidak |
| `program_studi` | Nama Program Studi | Ya (untuk Admin) |
| `angkatan` | Tahun angkatan (khusus Mahasiswa) | Tidak |
| `role` | Role pengguna (mahasiswa, dosen, dll) | Ya (khusus Admin) |

### Validasi dan Error

Jika terdapat data yang tidak valid (misal: email salah format atau role tidak terdaftar), sistem akan membatalkan seluruh proses import dan menampilkan daftar baris yang bermasalah.

### Tips

- Gunakan fitur **Export** terlebih dahulu untuk mendapatkan file dengan format yang benar, lalu edit file tersebut untuk diimpor kembali.
- Pastikan tidak ada baris kosong di tengah data Excel.
