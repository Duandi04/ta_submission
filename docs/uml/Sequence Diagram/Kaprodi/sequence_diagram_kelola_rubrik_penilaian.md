# Sequence Diagram - Kelola Rubrik Penilaian (Read, Create, Update, Delete)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola rubrik penilaian dan kriteria evaluasi tugas akhir, meliputi proses melihat daftar rubrik (Read), penambahan rubrik baru (Create), pembaruan data rubrik (Update), dan penghapusan rubrik (Delete). Langkah awal membuka halaman diletakkan di luar percabangan agar runtun secara logis.

```mermaid
sequenceDiagram
    actor Kaprodi as Lifeline1: Kaprodi
    participant UI as UI Kelola Rubrik Page
    participant Ctrl as Kaprodi\KaprodiController
    participant Svc as KaprodiService
    participant M_Rubric as Model Rubric

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN DAFTAR RUBRIK (COMMON STEP)
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Daftar Rubrik
    activate UI
    UI->>Ctrl: 2: rubrics()
    activate Ctrl
    Ctrl->>Svc: 3: getRubrics()
    activate Svc
    Svc->>M_Rubric: 4: Query all rubrics
    activate M_Rubric
    M_Rubric-->>Svc: 5: Rubrics Data
    deactivate M_Rubric
    Svc-->>Ctrl: 6: Rubrics List
    deactivate Svc
    Ctrl-->>UI: 7: Render Halaman Daftar Rubrik
    deactivate Ctrl
    UI-->>Kaprodi: 8: Menampilkan Daftar Rubrik Penilaian & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Rubrik Baru (Create)
        Kaprodi->>UI: 9: Mengisi detail rubrik & kriteria penilaian, lalu klik simpan
        activate UI
        UI->>Ctrl: 10: storeRubric(KaprodiRubricRequest)
        activate Ctrl
        Ctrl->>Svc: 11: createRubric(data)
        activate Svc
        Svc->>M_Rubric: 12: Rubric::create() & save criteria
        activate M_Rubric
        M_Rubric-->>Svc: 13: Rubric & Criteria Stored
        deactivate M_Rubric
        Svc-->>Ctrl: 14: Success
        deactivate Svc
        Ctrl-->>UI: 15: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16: Menampilkan Rubrik Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Rubrik (Update)
        Kaprodi->>UI: 9: Mengubah detail rubrik & kriteria, lalu klik perbarui
        activate UI
        UI->>Ctrl: 10: updateRubric(KaprodiRubricRequest, rubricId)
        activate Ctrl
        Ctrl->>Svc: 11: updateRubric(rubricId, data)
        activate Svc
        Svc->>M_Rubric: 12: Update rubric details & sync criteria
        activate M_Rubric
        M_Rubric-->>Svc: 13: Rubric Updated
        deactivate M_Rubric
        Svc-->>Ctrl: 14: Success
        deactivate Svc
        Ctrl-->>UI: 15: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16: Menampilkan Rubrik Terupdate di Daftar
        deactivate UI

    else Aksi: Hapus Rubrik (Delete)
        Kaprodi->>UI: 9: Mengklik tombol hapus rubrik
        activate UI
        UI->>Ctrl: 10: destroyRubric(rubricId)
        activate Ctrl
        Ctrl->>Svc: 11: deleteRubric(rubricId)
        activate Svc
        Svc->>M_Rubric: 12: Rubric::delete()
        activate M_Rubric
        M_Rubric-->>Svc: 13: Rubric Deleted
        deactivate M_Rubric
        Svc-->>Ctrl: 14: Success
        deactivate Svc
        Ctrl-->>UI: 15: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16: Rubrik Terhapus dari Daftar
        deactivate UI
    end
```
