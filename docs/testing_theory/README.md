# Panduan Teori Pengujian Perangkat Lunak (Software Testing Guide)

Selamat datang di direktori panduan teori pengujian perangkat lunak. Direktori ini dirancang secara khusus untuk memberikan pemahaman menyeluruh dan terstruktur mengenai metodologi, teknik, dan konsep di balik pengujian aplikasi, mulai dari pengujian tingkat dasar (*Unit Testing*) hingga analisis cakupan kode (*Code Coverage*) dan dampaknya terhadap kualitas sistem.

---

## 🗺️ Peta Navigasi Modul Pembelajaran

Panduan ini dibagi menjadi **7 modul utama** yang dapat dipelajari secara berurutan:

### 📑 1. Landasan & Dasar Pengujian
*   **[Modul 1: Pengantar Pengujian Perangkat Lunak](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/01_introduction.md)**
    *   Definisi kualitas perangkat lunak dan bug.
    *   Mengapa kita melakukan pengujian? (Aspek finansial, reputasi, dan keselamatan).
    *   Perbedaan antara Verifikasi (*Verification*) dan Validasi (*Validation*).
    *   Prinsip-prinsip utama dalam pengujian (Prinsip Dijkstra, dll.).

### 👥 2. Metodologi Pengujian
*   **[Modul 2: Black Box Testing (Pengujian Kotak Hitam)](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/02_black_box_testing.md)**
    *   Definisi dan esensi pengujian berbasis fungsionalitas.
    *   Teknik-teknik utama: *Equivalence Partitioning*, *Boundary Value Analysis*, *Decision Table Testing*, *State Transition Testing*.
    *   Penyusunan Skenario Positif vs Skenario Negatif.
*   **[Modul 3: White Box Testing (Pengujian Kotak Putih)](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/03_white_box_testing.md)**
    *   Definisi dan esensi pengujian berbasis struktur kode internal.
    *   Analisis Alur Kontrol (*Control Flow Graph*).
    *   Kriteria cakupan: *Statement*, *Branch/Decision*, *Path Coverage*.
    *   Konsep Kompleksitas Siklomatis (*Cyclomatic Complexity*).

### 🧪 3. Teknik Unit Testing & Asersi
*   **[Modul 4: Fondasi Dasar Unit Testing](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/04_unit_testing_fundamentals.md)**
    *   Ruang lingkup terkecil pengujian dan pentingnya isolasi kode.
    *   Konsep tiruan dependensi: *Mocks*, *Stubs*, *Fakes*, *Spies*, dan *Dummies*.
    *   Perbedaan mendasar antara *Unit Testing* vs *Integration Testing* vs *Feature/E2E Testing*.
*   **[Modul 5: Anatomi Penyusunan Unit Test](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/05_anatomy_of_unit_test.md)**
    *   Pola penyusunan tes AAA (*Arrange, Act, Assert*).
    *   Standar penamaan fungsi tes (*Naming Conventions*).
    *   Struktur asersi (*Assertion Styles*).
    *   Strategi pengujian database (*Database Testing Best Practices*).

### 📊 4. Metrik, Analisis, dan Dampak Sistem
*   **[Modul 6: Cara Perhitungan Code Coverage](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/06_code_coverage_calculation.md)**
    *   Kupas tuntas bagaimana persentase cakupan kode dihitung.
    *   Cara kerja alat bantu instrumentasi (*code instrumentation*) di PHP (Xdebug/PCOV).
    *   Perbedaan kalkulasi antara *Line Coverage*, *Branch Coverage*, dan *Path Coverage*.
*   **[Modul 7: Kualitas Kode, Keamanan, dan Keandalan](file:///c:/xampp/htdocs/ta_submission/docs/testing_theory/07_code_quality_and_reliability.md)**
    *   Hubungan antara unit test dengan *Code Quality* (*Clean Code*).
    *   Pengaruh pengujian terhadap keandalan (*reliability*) dan ketahanan dari bug regresi.
    *   Peran pengujian dalam keamanan (*security*) sistem (validasi parameter input dan RBAC).

---

## 🎯 Target Kompetensi Setelah Mempelajari Panduan Ini
Setelah membaca seluruh modul, Anda diharapkan mampu:
1.  **Merancang Skenario Uji**: Membuat uji kelayakan menggunakan batas ekstrim (*Boundary Value Analysis*) untuk mencegah kegagalan sistem.
2.  **Menulis Unit Test Terisolasi**: Menyusun test dengan pola AAA yang bersih, cepat, dan independen tanpa bergantung pada database eksternal sesungguhnya.
3.  **Membaca Laporan Coverage**: Mengidentifikasi titik lemah aplikasi berdasarkan metrik cakupan dan memperkuat asersi pengujian.
4.  **Meningkatkan Stabilitas Rilis**: Menjamin keamanan perubahan kode (*refactoring*) tanpa merusak fitur berjalan (*regression safety*).
