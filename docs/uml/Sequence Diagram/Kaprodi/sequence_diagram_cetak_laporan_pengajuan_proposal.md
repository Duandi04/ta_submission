# Sequence Diagram - Cetak Laporan Pengajuan Proposal

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mencetak laporan seluruh pengajuan proposal tugas akhir mahasiswa yang telah disetujui (Approved) beserta pembimbingnya melalui fitur print preview browser.

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Laporan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN LAPORAN
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Laporan
    activate UI
    UI->>Ctrl: 2: indexReport()
    activate Ctrl
    Ctrl->>Model: 3: getApprovedSubmissions()
    activate Model
    Model-->>Ctrl: 4: Data Laporan Mahasiswa & Pembimbing
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Daftar Laporan
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Daftar Laporan Mahasiswa & Pembimbing
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: CETAK LAPORAN
    %% ==========================================
    Kaprodi->>UI: 7: Mengklik Tombol Cetak Laporan
    activate UI
    UI->>Ctrl: 8: reportPrint()
    activate Ctrl
    Ctrl->>Model: 9: getApprovedSubmissions()
    activate Model
    Model-->>Ctrl: 10: Data Laporan Mahasiswa & Pembimbing
    deactivate Model
    Ctrl-->>UI: 11: Render Halaman Print View
    deactivate Ctrl
    UI->>UI: 12: Trigger Browser window.print()
    UI-->>Kaprodi: 13: Menampilkan Print Dialog Sistem & Cetak PDF / Hardcopy
    deactivate UI
```
