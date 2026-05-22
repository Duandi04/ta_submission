# Sequence Diagram - Kelola Rubrik Penilaian (Read, Create, Update, Delete)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola rubrik penilaian dan kriteria evaluasi tugas akhir, meliputi proses melihat daftar rubrik (Read), penambahan rubrik baru (Create), pembaruan data rubrik (Update), dan penghapusan rubrik (Delete).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Rubrik
    participant Ctrl as KaprodiController
    participant Model as Model Rubric

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN DAFTAR RUBRIK
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Daftar Rubrik
    activate UI
    UI-->>Kaprodi: 2: Menampilkan Daftar Rubrik Penilaian & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Rubrik Baru (Create)
        Kaprodi->>UI: 3a: Klik tombol "Tambah Rubrik"
        activate UI
        UI->>Ctrl: 4a: createRubric()
        activate Ctrl
        Ctrl-->>UI: 5a: Mengembalikan View Form Tambah Rubrik Baru
        deactivate Ctrl
        UI-->>Kaprodi: 6a: Menampilkan Form Tambah Rubrik Baru
        deactivate UI

        Kaprodi->>UI: 7a: Mengisi detail rubrik & kriteria penilaian, lalu klik simpan
        activate UI
        UI->>Ctrl: 8a: storeRubric(name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 9a: create(name, description, criteria)
        activate Model
        Model-->>Ctrl: 10a: Rubrik berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 11a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12a: Menampilkan Rubrik Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Rubrik (Update)
        Kaprodi->>UI: 3b: Klik tombol "Edit" pada salah satu rubrik
        activate UI
        UI->>Ctrl: 4b: editRubric(rubricId)
        activate Ctrl
        Ctrl->>Model: 5b: findOrFail(rubricId)
        activate Model
        Model-->>Ctrl: 6b: Data Rubrik
        deactivate Model
        Ctrl-->>UI: 7b: Mengembalikan View Form Edit Rubrik dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 8b: Menampilkan Form Edit Rubrik dengan data terisi
        deactivate UI

        Kaprodi->>UI: 9b: Mengubah detail rubrik & kriteria, lalu klik perbarui
        activate UI
        UI->>Ctrl: 10b: updateRubric(rubricId, name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 11b: update(name, description, criteria)
        activate Model
        Model-->>Ctrl: 12b: Rubrik berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 13b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14b: Menampilkan Rubrik Terupdate di Daftar
        deactivate UI

    else Aksi: Hapus Rubrik (Delete)
        Kaprodi->>UI: 3c: Mengklik tombol hapus rubrik
        activate UI
        UI->>Ctrl: 4c: destroyRubric(rubricId)
        activate Ctrl
        Ctrl->>Model: 5c: delete(rubricId)
        activate Model
        Model-->>Ctrl: 6c: Rubrik berhasil dihapus
        deactivate Model
        Ctrl-->>UI: 7c: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 8c: Rubrik Terhapus dari Daftar
        deactivate UI
    end
```
