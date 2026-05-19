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
        Kaprodi->>UI: 3: Mengisi detail rubrik & kriteria penilaian, lalu klik simpan
        activate UI
        UI->>Ctrl: 4: storeRubric(name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 5: create(name, description, criteria)
        activate Model
        Model-->>Ctrl: 6: Rubrik berhasil disimpan
        deactivate Model
        Ctrl-->>UI: 7: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 8: Menampilkan Rubrik Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Rubrik (Update)
        Kaprodi->>UI: 3: Mengubah detail rubrik & kriteria, lalu klik perbarui
        activate UI
        UI->>Ctrl: 4: updateRubric(rubricId, name, description, criteria)
        activate Ctrl
        Ctrl->>Model: 5: update(name, description, criteria)
        activate Model
        Model-->>Ctrl: 6: Rubrik berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 7: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 8: Menampilkan Rubrik Terupdate di Daftar
        deactivate UI

    else Aksi: Hapus Rubrik (Delete)
        Kaprodi->>UI: 3: Mengklik tombol hapus rubrik
        activate UI
        UI->>Ctrl: 4: destroyRubric(rubricId)
        activate Ctrl
        Ctrl->>Model: 5: delete(rubricId)
        activate Model
        Model-->>Ctrl: 6: Rubrik berhasil dihapus
        deactivate Model
        Ctrl-->>UI: 7: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 8: Rubrik Terhapus dari Daftar
        deactivate UI
    end
```
