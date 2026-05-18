# Sequence Diagram - Cetak Laporan Pengajuan Proposal

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mencetak laporan seluruh pengajuan proposal tugas akhir mahasiswa yang telah disetujui (Approved) beserta pembimbingnya melalui fitur print preview browser.

```mermaid
sequenceDiagram
    actor Kaprodi as Lifeline1: Kaprodi
    participant UI as UI Laporan Page
    participant Ctrl as Kaprodi\KaprodiController
    participant Model as Model User
    participant Submission as Model ThesisSubmission

    Kaprodi->>UI: 1: Membuka Menu Laporan Pengajuan Proposal
    activate UI
    UI->>Ctrl: 2: reportIndex()
    activate Ctrl
    Ctrl->>Model: 3: query Mahasiswa dengan status proposal 'approved'
    activate Model
    Model->>Submission: 4: fetch approved submissions & supervisors
    activate Submission
    Submission-->>Model: 5: Submissions Data
    deactivate Submission
    Model-->>Ctrl: 6: Students & Proposals Data
    deactivate Model
    Ctrl-->>UI: 7: Render Halaman Laporan Pengajuan
    deactivate Ctrl
    UI-->>Kaprodi: 8: Menampilkan Daftar Laporan Mahasiswa & Pembimbing
    deactivate UI

    Kaprodi->>UI: 9: Mengklik Tombol Cetak Laporan
    activate UI
    UI->>Ctrl: 10: reportPrint()
    activate Ctrl
    Ctrl->>Model: 11: query Mahasiswa dengan status proposal 'approved' (print-optimized)
    activate Model
    Model-->>Ctrl: 12: Students & Proposals Data
    deactivate Model
    Ctrl-->>UI: 13: Render Halaman Print View
    deactivate Ctrl
    UI->>UI: 14: Trigger Browser window.print()
    UI-->>Kaprodi: 15: Menampilkan Print Dialog Sistem & Cetak PDF / Hardcopy
    deactivate UI
```
