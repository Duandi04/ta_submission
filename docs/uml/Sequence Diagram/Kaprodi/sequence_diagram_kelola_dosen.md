# Sequence Diagram - Kelola Dosen (Read, Create, Update, Import, Export)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola data dosen, meliputi proses melihat daftar & detail (Read), tambah (Create), edit (Update), impor massal dari file (Import), dan ekspor data ke Excel (Export).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Dosen
    participant Ctrl as KaprodiController
    participant Excel as Facade Excel
    participant Model as Model User

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN KELOLA DOSEN
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Kelola Dosen
    activate UI
    UI-->>Kaprodi: 2: Menampilkan Halaman Daftar Dosen & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Dosen Baru (Create)
        Kaprodi->>UI: 3a: Klik tombol "Tambah Dosen"
        activate UI
        UI->>Ctrl: 4a: create()
        activate Ctrl
        Ctrl-->>UI: 5a: Mengembalikan View Form Tambah Dosen
        deactivate Ctrl
        UI-->>Kaprodi: 6a: Menampilkan Form Tambah Dosen
        deactivate UI
        
        Kaprodi->>UI: 7a: Mengisi form dosen baru & klik simpan
        activate UI
        UI->>Ctrl: 8a: store(name, nip, email)
        activate Ctrl
        Ctrl->>Model: 9a: create(name, nip, email, role = 'dosen')
        activate Model
        Model-->>Ctrl: 10a: Data dosen berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 11a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12a: Menampilkan Dosen Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Dosen (Update)
        Kaprodi->>UI: 3b: Klik tombol "Edit" pada salah satu dosen
        activate UI
        UI->>Ctrl: 4b: edit(lecturerId)
        activate Ctrl
        Ctrl->>Model: 5b: findOrFail(lecturerId)
        activate Model
        Model-->>Ctrl: 6b: Data Dosen
        deactivate Model
        Ctrl-->>UI: 7b: Mengembalikan View Form Edit Dosen dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 8b: Menampilkan Form Edit Dosen dengan data terisi
        deactivate UI

        Kaprodi->>UI: 9b: Mengubah data dosen & klik perbarui
        activate UI
        UI->>Ctrl: 10b: update(lecturerId, name, nip, email)
        activate Ctrl
        Ctrl->>Model: 11b: update(name, nip, email)
        activate Model
        Model-->>Ctrl: 12b: Data dosen berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 13b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14b: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Dosen Dari Excel (Import)
        Kaprodi->>UI: 3c: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 4c: import(excel_file)
        activate Ctrl
        Ctrl->>Excel: 5c: import(new DosenImport, excel_file)
        activate Excel
        Excel->>Model: 6c: Batch Create/Insert data dosen
        activate Model
        Model-->>Excel: 7c: Data disimpan ke DB
        deactivate Model
        Excel-->>Ctrl: 8c: Proses import selesai
        deactivate Excel
        Ctrl-->>UI: 9c: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 10c: Menampilkan Daftar Dosen Hasil Import
        deactivate UI

    else Aksi: Ekspor Dosen Ke Excel (Export)
        Kaprodi->>UI: 3d: Mengklik tombol Export Dosen
        activate UI
        UI->>Ctrl: 4d: export()
        activate Ctrl
        Ctrl->>Excel: 5d: download(new DosenExport, 'data_dosen.xlsx')
        activate Excel
        Excel->>Model: 6d: Get Collection / Query Dosen
        activate Model
        Model-->>Excel: 7d: Data Collection Dosen
        deactivate Model
        Excel-->>Ctrl: 8d: File Excel (.xlsx) Download Response
        deactivate Excel
        Ctrl-->>UI: 9d: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 10d: File Excel Terunduh Otomatis
        deactivate UI
    end
```
