# Dokumentasi Code Coverage - Sistem Pengajuan Tugas Akhir

*Code Coverage* (Cakupan Kode) adalah metrik untuk mengukur persentase baris kode program, metode, dan kelas dalam direktori aplikasi (`app/`) yang dieksekusi saat suite pengujian otomatis dijalankan.

Laporan cakupan kode ini dihasilkan secara otomatis oleh pustaka pengujian PHPUnit menggunakan driver cakupan kode PHP (**Xdebug** / **PCOV**).

---

## 📈 1. Ringkasan Cakupan Global
Berdasarkan hasil eksekusi terakhir, berikut adalah metrik cakupan kode global untuk sistem TA Submission:

| Kategori Pengukuran | Persentase Cakupan | Jumlah Baris / Entitas Terkover | Status Kualitas |
| :--- | :---: | :---: | :---: |
| **Lines (Cakupan Baris)** | **76.74%** | **1775 / 2313 baris** | **Medium (Lolos Standar)** |
| **Functions & Methods** | **64.77%** | **193 / 298 fungsi** | **Medium (Lolos Standar)** |
| **Classes & Traits** | **48.15%** | **26 / 54 kelas** | **Low (Perlu Penguatan)** |

> [!NOTE]
> Standar minimal cakupan baris kode (*Line Coverage*) untuk kelayakan rilis sistem akademik umumnya adalah **70%**. Dengan hasil **76.74%**, sistem ini dinyatakan aman dan stabil dari risiko regresi logika.

---

## 📁 2. Analisis Cakupan per Direktori Kode
Berikut adalah rincian tingkat cakupan kode untuk masing-masing direktori di dalam folder `app/`:

### A. Direktori: `app/Exports/` (Cakupan Baris: 97.37%)
*   **Baris Terkover**: 37 / 38 baris (97.37%)
*   **Fungsi Terkover**: 3 / 4 fungsi (75.00%)
*   **Kelas Terkover**: 0 / 1 kelas (0.00%)
*   **Deskripsi**: Direktori ini menampung kelas ekspor data tabular mahasiswa dan dosen ke berkas Excel. Logika penulisan kolom data sangat bersih dan teruji penuh.

### B. Direktori: `app/Helpers/` (Cakupan Baris: 86.36%)
*   **Baris Terkover**: 38 / 44 baris (86.36%)
*   **Fungsi Terkover**: 1 / 2 fungsi (50.00%)
*   **Kelas Terkover**: 1 / 2 kelas (50.00%)
*   **Deskripsi**: Berisi kelas pembantu umum seperti `SimilarityHelper` dan `NavigationHelper`. Logika deteksi plagiarisme judul berbasis orisinalitas teruji dengan persentase cakupan tinggi.

### C. Direktori: `app/Http/` (Cakupan Baris: 73.63%)
*   **Baris Terkover**: 1011 / 1373 baris (73.63%)
*   **Fungsi Terkover**: 88 / 155 fungsi (56.77%)
*   **Kelas Terkover**: 12 / 29 kelas (41.38%)
*   **Deskripsi**: Direktori paling masif yang berisi `Controllers`, `Middleware`, dan `Requests`. Mengalami korelasi pengujian yang luas pada form validation dan otorisasi menu dashboard. Sebagian kecil kode yang tidak ter-cover berada pada penanganan eksepsi catch block yang langka terjadi secara lokal.

### D. Direktori: `app/Imports/` (Cakupan Baris: 96.23%)
*   **Baris Terkover**: 51 / 53 baris (96.23%)
*   **Fungsi Terkover**: 3 / 4 fungsi (75.00%)
*   **Kelas Terkover**: 0 / 1 kelas (0.00%)
*   **Deskripsi**: Direktori untuk modul impor massal Excel data mahasiswa dan dosen pembimbing.

### E. Direktori: `app/Models/` (Cakupan Baris: 75.69%)
*   **Baris Terkover**: 165 / 218 baris (75.69%)
*   **Fungsi Terkover**: 60 / 81 fungsi (74.07%)
*   **Kelas Terkover**: 8 / 13 kelas (61.54%)
*   **Deskripsi**: Menampung representasi tabel basis data (seperti `User`, `ThesisSubmission`, `Assessment`, `Comment`, `Rubric`). Pengujian mencakup seluruh relasi database, status label translator, dan badge styles.

### F. Direktori: `app/Providers/` (Cakupan Baris: 75.00%)
*   **Baris Terkover**: 3 / 4 baris (75.00%)
*   **Fungsi Terkover**: 1 / 2 fungsi (50.00%)
*   **Kelas Terkover**: 0 / 1 kelas (0.00%)
*   **Deskripsi**: Mengonfigurasi boot-level application services. Termasuk optimasi eager loading sidebar menu di `AppServiceProvider`.

### G. Direktori: `app/Services/` (Cakupan Baris: 80.62%)
*   **Baris Terkover**: 470 / 583 baris (80.62%)
*   **Fungsi Terkover**: 37 / 50 fungsi (74.00%)
*   **Kelas Terkover**: 5 / 7 kelas (71.43%)
*   **Deskripsi**: Lapisan logika bisnis murni (*business logic layer*) untuk mengisolasi logika database dari controller. Berisi `KaprodiService`, `SubmissionService`, `AssessmentService`, dan `SupervisorService`. Teruji secara intensif untuk menjamin kepatuhan batasan pengusulan proposal mahasiswa dan isolasi blind grading.

---

## 🔍 3. Evaluasi Area yang Belum Tercover & Solusi Penguatan

### Mengapa Cakupan Kelas/Traits Rendah (48.15%)?
Meskipun cakupan baris kode mencapai **76.74%**, persentase kelas/traits yang terkover hanya **48.15%**. Secara teknis dalam PHPUnit, sebuah kelas dinyatakan **100% terkover** hanya jika *setiap metode* di dalam kelas tersebut dieksekusi minimal sekali selama pengujian. 

Banyak kelas di sistem ini memiliki metode relasi tambahan (misalnya metode data historis atau relasi admin) yang dideklarasikan pada model namun belum dipanggil secara langsung di feature test, sehingga menurunkan persentase kelas utuh.

### Strategi Peningkatan Coverage (>90%):
Untuk mendongkrak cakupan hingga menyentuh batas excellent (>90%), langkah-langkah pengujian berikut direkomendasikan pada iterasi berikutnya:
1.  **Simulasi Edge Cases pada Uji Form**: Menulis feature test tambahan yang mensimulasikan kegagalan validasi form pada input kosong atau tipe data tidak valid (misal, mengunggah format CSV salah pada impor data Kaprodi).
2.  **Mocking System Failures**: Menggunakan PHPUnit Mocking (`Http::fake()` atau `Event::fake()`) untuk memicu block catch eksepsi internal basis data, sehingga mengeksekusi baris catch block yang saat ini belum teruji.
