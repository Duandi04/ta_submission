# Sequence Diagram - Pengaturan TA

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) melihat dan memperbarui konfigurasi atau pengaturan tugas akhir program studi (misalnya batas tanggal pengajuan, format dokumen, limit revisi, dll.).

```mermaid
sequenceDiagram
    actor Kaprodi as Lifeline1: Kaprodi
    participant UI as UI Pengaturan TA Page
    participant Ctrl as Kaprodi\KaprodiController
    participant Svc as KaprodiService
    participant Model as Model Setting

    Kaprodi->>UI: 1: Membuka Halaman Pengaturan TA
    activate UI
    UI->>Ctrl: 2: settings()
    activate Ctrl
    Ctrl->>Svc: 3: getSettings()
    activate Svc
    Svc->>Model: 4: Query all settings
    activate Model
    Model-->>Svc: 5: Settings Data
    deactivate Model
    Svc-->>Ctrl: 6: Settings List
    deactivate Svc
    Ctrl-->>UI: 7: Render Halaman Pengaturan
    deactivate Ctrl
    UI-->>Kaprodi: 8: Menampilkan Konfigurasi Sistem Saat Ini
    deactivate UI

    Kaprodi->>UI: 9: Mengubah pengaturan (batas tanggal, kuota bimbingan, file extension, dll.) & klik simpan
    activate UI
    UI->>Ctrl: 10: updateSettings(KaprodiSettingsRequest)
    activate Ctrl
    Ctrl->>Svc: 11: updateSettings(data)
    activate Svc
    Svc->>Model: 12: update or create settings keys
    activate Model
    Model-->>Svc: 13: Settings Saved in DB
    deactivate Model
    Svc-->>Ctrl: 14: Success
    deactivate Svc
    Ctrl-->>UI: 15: Redirect Back dengan Pesan Sukses
    deactivate Ctrl
    UI-->>Kaprodi: 16: Menampilkan Pengaturan Terbaru yang Telah Diterapkan
    deactivate UI
```
