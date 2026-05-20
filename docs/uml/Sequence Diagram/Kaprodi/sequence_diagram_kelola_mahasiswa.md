# Sequence Diagram - Kelola Mahasiswa (Read, Create, Update, Import, Export)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola data mahasiswa, meliputi proses melihat daftar & detail (Read), tambah (Create), edit (Update), impor massal dari file (Import), dan ekspor data ke Excel (Export).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Mahasiswa
    participant Ctrl as KaprodiController
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
        Kaprodi->>UI: 3: Klik tombol "Tambah Mahasiswa"
        activate UI
        UI->>Ctrl: 4: create()
        activate Ctrl
        Ctrl-->>UI: 5: Menampilkan Form Tambah Mahasiswa
        deactivate Ctrl
        deactivate UI
        
        Kaprodi->>UI: 6: Mengisi form mahasiswa baru & klik simpan
        activate UI
        UI->>Ctrl: 7: store(name, nim, email)
        activate Ctrl
        Ctrl->>Model: 8: create(name, nim, email, role = 'mahasiswa')
        activate Model
        Model-->>Ctrl: 9: Data mahasiswa berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 10: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 11: Menampilkan Mahasiswa Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Mahasiswa (Update)
        Kaprodi->>UI: 3: Klik tombol "Edit" pada salah satu mahasiswa
        activate UI
        UI->>Ctrl: 4: edit(studentId)
        activate Ctrl
        Ctrl->>Model: 5: findOrFail(studentId)
        activate Model
        Model-->>Ctrl: 6: Data Mahasiswa
        deactivate Model
        Ctrl-->>UI: 7: Menampilkan Form Edit Mahasiswa dengan data terisi
        deactivate Ctrl
        deactivate UI

        Kaprodi->>UI: 8: Mengubah data mahasiswa & klik perbarui
        activate UI
        UI->>Ctrl: 9: update(studentId, name, nim, email)
        activate Ctrl
        Ctrl->>Model: 10: update(name, nim, email)
        activate Model
        Model-->>Ctrl: 11: Data mahasiswa berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 12: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 13: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Mahasiswa Dari Excel (Import)
        Kaprodi->>UI: 3: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 4: import(excel_file)
        activate Ctrl
        Ctrl->>Model: 5: importFromExcel(excel_file)
        activate Model
        Model-->>Ctrl: 6: Data mahasiswa berhasil diimpor
        deactivate Model
        Ctrl-->>UI: 7: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 8: Menampilkan Daftar Mahasiswa Hasil Import
        deactivate UI

    else Aksi: Ekspor Mahasiswa Ke Excel (Export)
        Kaprodi->>UI: 3: Mengklik tombol Export Mahasiswa
        activate UI
        UI->>Ctrl: 4: export()
        activate Ctrl
        Ctrl->>Model: 5: exportToExcel()
        activate Model
        Model-->>Ctrl: 6: File Excel (.xlsx)
        deactivate Model
        Ctrl-->>UI: 7: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 8: File Excel Terunduh Otomatis
        deactivate UI
    end
```
