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
        Kaprodi->>UI: 3: Klik tombol "Tambah Dosen"
        activate UI
        UI->>Ctrl: 4: create()
        activate Ctrl
        Ctrl-->>UI: 5: Mengembalikan View Form Tambah Dosen
        deactivate Ctrl
        UI-->>Kaprodi: 5b: Menampilkan Form Tambah Dosen
        deactivate UI
        
        Kaprodi->>UI: 6: Mengisi form dosen baru & klik simpan
        activate UI
        UI->>Ctrl: 7: store(name, nip, email)
        activate Ctrl
        Ctrl->>Model: 8: create(name, nip, email, role = 'dosen')
        activate Model
        Model-->>Ctrl: 9: Data dosen berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 10: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 11: Menampilkan Dosen Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Dosen (Update)
        Kaprodi->>UI: 3: Klik tombol "Edit" pada salah satu dosen
        activate UI
        UI->>Ctrl: 4: edit(lecturerId)
        activate Ctrl
        Ctrl->>Model: 5: findOrFail(lecturerId)
        activate Model
        Model-->>Ctrl: 6: Data Dosen
        deactivate Model
        Ctrl-->>UI: 7: Mengembalikan View Form Edit Dosen dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 7b: Menampilkan Form Edit Dosen dengan data terisi
        deactivate UI

        Kaprodi->>UI: 8: Mengubah data dosen & klik perbarui
        activate UI
        UI->>Ctrl: 9: update(lecturerId, name, nip, email)
        activate Ctrl
        Ctrl->>Model: 10: update(name, nip, email)
        activate Model
        Model-->>Ctrl: 11: Data dosen berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 12: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 13: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Dosen Dari Excel (Import)
        Kaprodi->>UI: 3: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 4: import(excel_file)
        activate Ctrl
        Ctrl->>Excel: 5: import(new DosenImport, excel_file)
        activate Excel
        Excel->>Model: 6: Batch Create/Insert data dosen
        activate Model
        Model-->>Excel: 7: Data disimpan ke DB
        deactivate Model
        Excel-->>Ctrl: 8: Proses import selesai
        deactivate Excel
        Ctrl-->>UI: 9: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 10: Menampilkan Daftar Dosen Hasil Import
        deactivate UI

    else Aksi: Ekspor Dosen Ke Excel (Export)
        Kaprodi->>UI: 3: Mengklik tombol Export Dosen
        activate UI
        UI->>Ctrl: 4: export()
        activate Ctrl
        Ctrl->>Excel: 5: download(new DosenExport, 'data_dosen.xlsx')
        activate Excel
        Excel->>Model: 6: Get Collection / Query Dosen
        activate Model
        Model-->>Excel: 7: Data Collection Dosen
        deactivate Model
        Excel-->>Ctrl: 8: File Excel (.xlsx) Download Response
        deactivate Excel
        Ctrl-->>UI: 9: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 10: File Excel Terunduh Otomatis
        deactivate UI
    end
```
