# Sequence Diagram - Pengaturan TA

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) melihat dan memperbarui konfigurasi atau pengaturan tugas akhir program studi (maksimal batch pengajuan, maksimal pengajuan per batch).

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Pengaturan TA
    participant Ctrl as KaprodiController
    participant Model as Model Setting

    Kaprodi->>UI: 1: Membuka Halaman Pengaturan
    activate UI
    UI-->>Kaprodi: 2: Menampilkan Halaman Pengaturan sistem
    deactivate UI

    Kaprodi->>UI: 3: Mengubah pengaturan batch sistem dan pengajuan per batch
    Kaprodi->>UI: 4: Klik menyimpan konfigurasi
    activate UI
    UI->>Ctrl: 5: updateSettings(max_batches, attempts_per_batch)
    activate Ctrl
    Ctrl->>Model: 6: update(max_batches, attempts_per_batch)
    activate Model
    Model-->>Ctrl: 7: Konfigurasi berhasil disimpan
    deactivate Model
    Ctrl-->>UI: 8: Redirect Back dengan Pesan Sukses
    deactivate Ctrl
    UI-->>Kaprodi: 9: Selesai (Menampilkan pengaturan terbaru)
    deactivate UI
```
