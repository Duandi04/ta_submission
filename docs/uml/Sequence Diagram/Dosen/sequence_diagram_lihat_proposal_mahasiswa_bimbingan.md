# Sequence Diagram - Lihat Proposal Mahasiswa Bimbingan

Diagram ini menunjukkan interaksi sistem saat Dosen melihat daftar pengajuan tugas akhir/proposal dari mahasiswa bimbingannya.

```mermaid
sequenceDiagram
    actor Dosen as Lifeline1: Dosen
    participant UI as UI Dosen Dashboard
    participant Ctrl as Dosen\SubmissionController
    participant Model as Model ThesisSubmission

    Dosen->>UI: 1: Membuka Menu Bimbingan Mahasiswa
    activate UI
    UI->>Ctrl: 2: index(request) / bimbingan()
    activate Ctrl
    Ctrl->>Model: 3: query bimbingan (where supervisor_id = dosen_id)
    activate Model
    Model-->>Ctrl: 4: Data Proposal & Mahasiswa Bimbingan
    deactivate Model
    Ctrl-->>UI: 5: Render Halaman Bimbingan
    deactivate Ctrl
    UI-->>Dosen: 6: Menampilkan Daftar Proposal Mahasiswa Bimbingan
    deactivate UI
```
