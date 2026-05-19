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
    UI-->>Kaprodi: 2: Menampilkan Daftar Laporan Mahasiswa & Pembimbing
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: CETAK LAPORAN
    %% ==========================================
    Kaprodi->>UI: 3: Mengklik Tombol Cetak Laporan
    activate UI
    UI->>Ctrl: 4: reportPrint()
    activate Ctrl
    Ctrl->>Model: 5: getApprovedSubmissions()
    activate Model
    Model-->>Ctrl: 6: Data Laporan Mahasiswa & Pembimbing
    deactivate Model
    Ctrl-->>UI: 7: Render Halaman Print View
    deactivate Ctrl
    UI->>UI: 8: Trigger Browser window.print()
    UI-->>Kaprodi: 9: Menampilkan Print Dialog Sistem & Cetak PDF / Hardcopy
    deactivate UI
```
