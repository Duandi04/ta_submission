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
    UI->>Ctrl: 2: indexRubric()
    activate Ctrl
    Ctrl->>Model: 3: all()
    activate Model
    Model-->>Ctrl: 4: Koleksi Data Rubrik Penilaian
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Daftar Rubrik
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Daftar Rubrik Penilaian & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Rubrik Baru (Create)
        Kaprodi->>UI: 7a: Klik tombol "Tambah Rubrik"
        activate UI
        UI->>Ctrl: 8a: createRubric()
        activate Ctrl
        Ctrl-->>UI: 9a: Mengembalikan View Form Tambah Rubrik Baru
        deactivate Ctrl
        UI-->>Kaprodi: 10a: Menampilkan Form Tambah Rubrik Baru
        deactivate UI

        Kaprodi->>UI: 11a: Mengisi detail rubrik & kriteria penilaian, lalu klik simpan
        activate UI
        UI->>Ctrl: 12a: storeRubric(name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 13a: create(name, description, criteria)
        activate Model
        Model-->>Ctrl: 14a: Rubrik berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 15a: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16a: Menampilkan Rubrik Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Rubrik (Update)
        Kaprodi->>UI: 7b: Klik tombol "Edit" pada salah satu rubrik
        activate UI
        UI->>Ctrl: 8b: editRubric(rubricId)
        activate Ctrl
        Ctrl->>Model: 9b: findOrFail(rubricId)
        activate Model
        Model-->>Ctrl: 10b: Data Rubrik
        deactivate Model
        Ctrl-->>UI: 11b: Mengembalikan View Form Edit Rubrik dengan data terisi
        deactivate Ctrl
        UI-->>Kaprodi: 12b: Menampilkan Form Edit Rubrik dengan data terisi
        deactivate UI

        Kaprodi->>UI: 13b: Mengubah detail rubrik & kriteria, lalu klik perbarui
        activate UI
        UI->>Ctrl: 14b: updateRubric(rubricId, name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 15b: update(name, description, criteria)
        activate Model
        Model-->>Ctrl: 16b: Rubrik berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 17b: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 18b: Menampilkan Rubrik Terupdate di Daftar
        deactivate UI

    else Aksi: Hapus Rubrik (Delete)
        Kaprodi->>UI: 7c: Mengklik tombol hapus rubrik
        activate UI
        UI->>Ctrl: 8c: destroyRubric(rubricId)
        activate Ctrl
        Ctrl->>Model: 9c: delete(rubricId)
        activate Model
        Model-->>Ctrl: 10c: Rubrik berhasil dihapus
        deactivate Model
        Ctrl-->>UI: 11c: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12c: Rubrik Terhapus dari Daftar
        deactivate UI
    end
```
