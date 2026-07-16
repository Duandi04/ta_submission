# Sequence Diagram - Pantau Progres Penilaian

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat memantau progres penilaian berkas yang berstatus **"Sedang Ditinjau"** di mana masih ada dosen penilai yang belum selesai menginput nilai mereka.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/Kelola%20Daftar%20Pengajuan/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Memantau Progres Penilaian

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Membuka halaman detail pengajuan
    activate UI
    UI->>Ctrl: 2: submissionShow(id)
    activate Ctrl
    Ctrl->>Model: 3: with(['student', 'assessments.evaluator'])->findOrFail(id)
    activate Model
    Model-->>Ctrl: 4: Data pengajuan & progres penilaian
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan view detail pengajuan (dengan status progres penilaian)
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan progres penilaian (pesan menunggu penilaian selesai)
    deactivate UI
```
