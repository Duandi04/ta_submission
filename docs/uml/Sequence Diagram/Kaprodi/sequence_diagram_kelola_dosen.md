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
    UI->>Ctrl: 2: index()
    activate Ctrl
    Ctrl->>Model: 3: where('role', 'dosen')->get()
    activate Model
    Model-->>Ctrl: 4: Koleksi Data Dosen
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Daftar Dosen
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Halaman Daftar Dosen & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Dosen Baru (Create)
        Kaprodi->>UI: 7a: Klik tombol "Tambah Dosen"
        activate UI
        UI->>Ctrl: 8a: create()
        activate Ctrl
        Ctrl-->>UI: 9a: Mengembalikan View Form Tambah Dosen
        deactivate Ctrl
        UI-->>Kaprodi: 10a: Menampilkan Form Tambah Dosen
        deactivate UI
        
        Kaprodi->>UI: 11a: Mengisi form dosen baru & klik simpan
        activate UI
        UI->>Ctrl: 12a: store(name, nip, email)
        activate Ctrl
        Ctrl->>Model: 13a: create(name, nip, email, role = 'dosen')
        activate Model
        Model-->>Ctrl: 14a: Data dosen berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 15a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16a: Menampilkan Dosen Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Dosen (Update)
        Kaprodi->>UI: 7b: Klik tombol "Edit" pada salah satu dosen
        activate UI
        UI->>Ctrl: 8b: edit(lecturerId)
        activate Ctrl
        Ctrl->>Model: 9b: findOrFail(lecturerId)
        activate Model
        Model-->>Ctrl: 10b: Data Dosen
        deactivate Model
        Ctrl-->>UI: 11b: Mengembalikan View Form Edit Dosen dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 12b: Menampilkan Form Edit Dosen dengan data terisi
        deactivate UI

        Kaprodi->>UI: 13b: Mengubah data dosen & klik perbarui
        activate UI
        UI->>Ctrl: 14b: update(lecturerId, name, nip, email)
        activate Ctrl
        Ctrl->>Model: 15b: update(name, nip, email)
        activate Model
        Model-->>Ctrl: 16b: Data dosen berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 17b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 18b: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Dosen Dari Excel (Import)
        Kaprodi->>UI: 7c: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 8c: import(excel_file)
        activate Ctrl
        Ctrl->>Excel: 9c: import(new DosenImport, excel_file)
        activate Excel
        Excel->>Model: 10c: Batch Create/Insert data dosen
        activate Model
        Model-->>Excel: 11c: Data disimpan ke DB
        deactivate Model
        Excel-->>Ctrl: 12c: Proses import selesai
        deactivate Excel
        Ctrl-->>UI: 13c: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14c: Menampilkan Daftar Dosen Hasil Import
        deactivate UI

    else Aksi: Ekspor Dosen Ke Excel (Export)
        Kaprodi->>UI: 7d: Mengklik tombol Export Dosen
        activate UI
        UI->>Ctrl: 8d: export()
        activate Ctrl
        Ctrl->>Excel: 9d: download(new DosenExport, 'data_dosen.xlsx')
        activate Excel
        Excel->>Model: 10d: Get Collection / Query Dosen
        activate Model
        Model-->>Excel: 11d: Data Collection Dosen
        deactivate Model
        Excel-->>Ctrl: 12d: File Excel (.xlsx) Download Response
        deactivate Excel
        Ctrl-->>UI: 13d: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 14d: File Excel Terunduh Otomatis
        deactivate UI
    end
```
