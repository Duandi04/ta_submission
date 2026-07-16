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
    UI->>Ctrl: 2: index()
    activate Ctrl
    Ctrl->>Model: 3: where('role', 'mahasiswa')->get()
    activate Model
    Model-->>Ctrl: 4: Koleksi Data Mahasiswa
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Daftar Mahasiswa
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Halaman Daftar Mahasiswa & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Mahasiswa Baru (Create)
        Kaprodi->>UI: 7a: Klik tombol "Tambah Mahasiswa"
        activate UI
        UI->>Ctrl: 8a: create()
        activate Ctrl
        Ctrl-->>UI: 9a: Mengembalikan View Form Tambah Mahasiswa
        deactivate Ctrl
        UI-->>Kaprodi: 10a: Menampilkan Form Tambah Mahasiswa
        deactivate UI
        
        Kaprodi->>UI: 11a: Mengisi form mahasiswa baru & klik simpan
        activate UI
        UI->>Ctrl: 12a: store(name, nim, email)
        activate Ctrl
        Ctrl->>Model: 13a: create(name, nim, email, role = 'mahasiswa')
        activate Model
        Model-->>Ctrl: 14a: Data mahasiswa berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 15a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16a: Menampilkan Mahasiswa Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Mahasiswa (Update)
        Kaprodi->>UI: 7b: Klik tombol "Edit" pada salah satu mahasiswa
        activate UI
        UI->>Ctrl: 8b: edit(studentId)
        activate Ctrl
        Ctrl->>Model: 9b: findOrFail(studentId)
        activate Model
        Model-->>Ctrl: 10b: Data Mahasiswa
        deactivate Model
        Ctrl-->>UI: 11b: Mengembalikan View Form Edit Mahasiswa dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 12b: Menampilkan Form Edit Mahasiswa dengan data terisi
        deactivate UI

        Kaprodi->>UI: 13b: Mengubah data mahasiswa & klik perbarui
        activate UI
        UI->>Ctrl: 14b: update(studentId, name, nim, email)
        activate Ctrl
        Ctrl->>Model: 15b: update(name, nim, email)
        activate Model
        Model-->>Ctrl: 16b: Data mahasiswa berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 17b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 18b: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Mahasiswa Dari Excel (Import)
        Kaprodi->>UI: 7c: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 8c: import(excel_file)
        activate Ctrl
        Ctrl->>Excel: 9c: import(new MahasiswaImport, excel_file)
        activate Excel
        Excel->>Model: 10c: Batch Create/Insert data mahasiswa
        activate Model
        Model-->>Excel: 11c: Data disimpan ke DB
        deactivate Model
        Excel-->>Ctrl: 12c: Proses import selesai
        deactivate Excel
        Ctrl-->>UI: 13c: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14c: Menampilkan Daftar Mahasiswa Hasil Import
        deactivate UI

    else Aksi: Ekspor Mahasiswa Ke Excel (Export)
        Kaprodi->>UI: 7d: Mengklik tombol Export Mahasiswa
        activate UI
        UI->>Ctrl: 8d: export()
        activate Ctrl
        Ctrl->>Excel: 9d: download(new MahasiswaExport, 'data_mahasiswa.xlsx')
        activate Excel
        Excel->>Model: 10d: Get Collection / Query Mahasiswa
        activate Model
        Model-->>Excel: 11d: Data Collection Mahasiswa
        deactivate Model
        Excel-->>Ctrl: 12d: File Excel (.xlsx) Download Response
        deactivate Excel
        Ctrl-->>UI: 13d: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 14d: File Excel Terunduh Otomatis
        deactivate UI
    end
```
