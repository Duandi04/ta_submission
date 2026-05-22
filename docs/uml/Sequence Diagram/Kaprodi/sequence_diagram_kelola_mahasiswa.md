# Sequence Diagram - Kelola Mahasiswa (Read, Create, Update, Import, Export)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola data mahasiswa, meliputi proses melihat daftar & detail (Read), tambah (Create), edit (Update), impor massal dari file (Import), dan ekspor data ke Excel (Export).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Mahasiswa
    participant Ctrl as KaprodiController
    participant Excel as Facade Excel
    participant Model as Model User

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN KELOLA MAHASISWA
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Kelola Mahasiswa
    activate UI
    UI-->>Kaprodi: 2: Menampilkan Halaman Daftar Mahasiswa & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Mahasiswa Baru (Create)
        Kaprodi->>UI: 3a: Klik tombol "Tambah Mahasiswa"
        activate UI
        UI->>Ctrl: 4a: create()
        activate Ctrl
        Ctrl-->>UI: 5a: Mengembalikan View Form Tambah Mahasiswa
        deactivate Ctrl
        UI-->>Kaprodi: 6a: Menampilkan Form Tambah Mahasiswa
        deactivate UI
        
        Kaprodi->>UI: 7a: Mengisi form mahasiswa baru & klik simpan
        activate UI
        UI->>Ctrl: 8a: store(name, nim, email)
        activate Ctrl
        Ctrl->>Model: 9a: create(name, nim, email, role = 'mahasiswa')
        activate Model
        Model-->>Ctrl: 10a: Data mahasiswa berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 11a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12a: Menampilkan Mahasiswa Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Mahasiswa (Update)
        Kaprodi->>UI: 3b: Klik tombol "Edit" pada salah satu mahasiswa
        activate UI
        UI->>Ctrl: 4b: edit(studentId)
        activate Ctrl
        Ctrl->>Model: 5b: findOrFail(studentId)
        activate Model
        Model-->>Ctrl: 6b: Data Mahasiswa
        deactivate Model
        Ctrl-->>UI: 7b: Mengembalikan View Form Edit Mahasiswa dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 8b: Menampilkan Form Edit Mahasiswa dengan data terisi
        deactivate UI

        Kaprodi->>UI: 9b: Mengubah data mahasiswa & klik perbarui
        activate UI
        UI->>Ctrl: 10b: update(studentId, name, nim, email)
        activate Ctrl
        Ctrl->>Model: 11b: update(name, nim, email)
        activate Model
        Model-->>Ctrl: 12b: Data mahasiswa berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 13b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14b: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Mahasiswa Dari Excel (Import)
        Kaprodi->>UI: 3c: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 4c: import(excel_file)
        activate Ctrl
        Ctrl->>Excel: 5c: import(new MahasiswaImport, excel_file)
        activate Excel
        Excel->>Model: 6c: Batch Create/Insert data mahasiswa
        activate Model
        Model-->>Excel: 7c: Data disimpan ke DB
        deactivate Model
        Excel-->>Ctrl: 8c: Proses import selesai
        deactivate Excel
        Ctrl-->>UI: 9c: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 10c: Menampilkan Daftar Mahasiswa Hasil Import
        deactivate UI

    else Aksi: Ekspor Mahasiswa Ke Excel (Export)
        Kaprodi->>UI: 3d: Mengklik tombol Export Mahasiswa
        activate UI
        UI->>Ctrl: 4d: export()
        activate Ctrl
        Ctrl->>Excel: 5d: download(new MahasiswaExport, 'data_mahasiswa.xlsx')
        activate Excel
        Excel->>Model: 6d: Get Collection / Query Mahasiswa
        activate Model
        Model-->>Excel: 7d: Data Collection Mahasiswa
        deactivate Model
        Excel-->>Ctrl: 8d: File Excel (.xlsx) Download Response
        deactivate Excel
        Ctrl-->>UI: 9d: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 10d: File Excel Terunduh Otomatis
        deactivate UI
    end
```
