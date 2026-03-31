# Panduan Pengujian (Testing Guide)

Aplikasi ini dilengkapi dengan suite pengujian otomatis menggunakan **PHPUnit** untuk menjamin stabilitas fungsionalitas inti.

---

## 1. Menjalankan Pengujian

### Semua Pengujian

```bash
php artisan test
```

### Pengujian Spesifik (Kaprodi)

```bash
# Unit Test: Logika bisnis dan efisiensi query
php artisan test tests/Unit/KaprodiServiceTest.php

# Feature Test: UI, Security, dan Alur Kerja (CRUD, Laporan, Sorting)
php artisan test tests/Feature/KaprodiStudentManagementTest.php
```

---

## 2. Cakupan Pengujian (Test Coverage)

### Unit Tests (`tests/Unit`)

Fokus pada validasi logika murni tanpa menyentuh server HTTP:

- **KaprodiService**: Memastikan batas maksimal 1 proposal diterima per mahasiswa, validasi status pengajuan, dan efisiensi query eager loading.

### Feature Tests (`tests/Feature`)

Mensimulasikan interaksi pengguna nyata:

- **Authentication**: Login, logout, dan proteksi route berdasarkan role.
- **Student Management**: CRUD mahasiswa, validasi keunikan NIM/Email, dan filter pencarian.
- **Reporting**: Keberhasilan render halaman laporan dan validasi urutan (sorting) berdasarkan NIM.
- **Security Check**: Memastikan Kaprodi tidak bisa mengakses data prodi lain (403 Forbidden).

---

## 3. Tips Pengembangan

- **Database Testing**: Pengujian menggunakan database di memori (SQLite) secara default untuk kecepatan.
- **Seeders**: Gunakan `DummyProposalSeeder` untuk mengisi data awal yang realistis guna pengujian manual di browser.
- **Assertions**: Selalu sertakan pengecekan status code HTTP, keberadaan teks di view, dan perubahan record di database.
