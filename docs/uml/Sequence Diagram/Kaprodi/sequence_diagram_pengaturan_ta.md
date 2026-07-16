# Sequence Diagram - Pengaturan TA

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) melihat dan memperbarui konfigurasi atau pengaturan tugas akhir program studi (maksimal batch pengajuan, maksimal pengajuan per batch).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Pengaturan TA
    participant Ctrl as KaprodiController
    participant Model as Model ProgramStudi

    Kaprodi->>UI: 1: Membuka Halaman Pengaturan
    activate UI
    UI->>Ctrl: 2: editSettings()
    activate Ctrl
    Ctrl->>Model: 3: first()
    activate Model
    Model-->>Ctrl: 4: Data Konfigurasi Program Studi
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Pengaturan Sistem TA
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Halaman Pengaturan Sistem TA (dengan data max_batches, attempts_per_batch, submission_start, submission_end)
    deactivate UI

    Kaprodi->>UI: 7: Mengubah parameter batch, attempts, & rentang waktu (deadline) pengajuan
    Kaprodi->>UI: 8: Klik tombol Simpan Konfigurasi
    activate UI
    UI->>Ctrl: 9: updateSettings(max_batches, attempts_per_batch, submission_start, submission_end)
    activate Ctrl
    Ctrl->>Model: 10: update(max_batches, attempts_per_batch, submission_start, submission_end)
    activate Model
    Model-->>Ctrl: 11: Konfigurasi Program Studi berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 12: Redirect Back dengan Pesan Sukses
    deactivate Ctrl
    UI-->>Kaprodi: 13: Selesai (Menampilkan halaman dengan konfigurasi terupdate)
    deactivate UI
```
