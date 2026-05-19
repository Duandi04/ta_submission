# Tabel Skenario - Kelola Rubrik Penilaian

| Elemen Skenario | Keterangan |
| --- | --- |
| **Nama Fitur** | Kelola Rubrik Penilaian (Read, Create, Update, Delete) |
| **Aktor** | Kaprodi (Ketua Program Studi) |
| **Deskripsi** | Menggambarkan proses Kaprodi mengelola rubrik-rubrik penilaian sidang dan kriteria evaluasi pendukungnya |
| **Kondisi Awal** | Kaprodi telah masuk ke sistem dan berada di Dashboard Kaprodi |
| **Kondisi Akhir** | Rubrik dan kriteria penilaian tersimpan, diubah, atau dihapus secara permanen di database |

## 1. Skenario A: Read (Melihat Daftar Rubrik)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih menu "Kelola Rubrik" | Mengirimkan request ke `KaprodiController->rubrics()` |
| 2 | - | Query data rubrik aktif dari model `Rubric` beserta kriterianya |
| 3 | - | Menampilkan Halaman Daftar Rubrik Penilaian berisi nama rubrik, jumlah kriteria, dan opsi tindakan |

---

## 2. Skenario B: Create (Tambah Rubrik Baru)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Tambah Rubrik" | Menampilkan Form Tambah Rubrik & Kriteria Penilaian |
| 2 | Kaprodi mengisi nama rubrik dan menambahkan baris kriteria penilaian (nama kriteria, bobot persen), lalu klik "Simpan" | Memvalidasi input form (akumulasi bobot kriteria harus sama dengan 100%) |
| 3 | - | Validasi Sukses: Membuat record rubrik baru di model `Rubric` dengan data kriteria tersimpan langsung dalam atribut JSON `criteria` |
| 4 | - | Mengalihkan kembali ke daftar rubrik dengan pesan sukses *"Rubrik berhasil ditambahkan"* |

---

## 3. Skenario C: Update (Mengubah Rubrik)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi memilih salah satu rubrik lalu mengklik "Edit" | Menampilkan Form Edit Rubrik dengan data saat ini terisi |
| 2 | Kaprodi memperbarui nama rubrik atau persentase bobot kriteria, lalu klik "Perbarui" | Memvalidasi input data perubahan (total bobot kriteria tetap harus 100%) |
| 3 | - | Validasi Sukses: Menyimpan pembaruan data rubrik beserta daftar kriteria yang telah diperbarui ke atribut JSON `criteria` |
| 4 | - | Mengalihkan kembali ke daftar dengan pesan sukses *"Rubrik berhasil diperbarui"* |

---

## 4. Skenario D: Delete (Menghapus Rubrik)

| Langkah | Aksi Aktor (User) | Reaksi Sistem (System) |
| :---: | --- | --- |
| 1 | Kaprodi mengklik tombol "Hapus" pada rubrik tertentu | Menampilkan modal dialog konfirmasi penghapusan |
| 2 | Kaprodi mengklik "Ya, Hapus" | Mengirimkan permintaan hapus ke `KaprodiController->destroyRubric(id)` |
| 3 | - | Menghapus data rubrik (otomatis menghapus seluruh kriteria di dalamnya) dari database |
| 4 | - | Menampilkan notifikasi sukses *"Rubrik berhasil dihapus"* |
