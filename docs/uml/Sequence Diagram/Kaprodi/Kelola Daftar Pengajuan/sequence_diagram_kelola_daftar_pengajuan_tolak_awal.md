# Sequence Diagram - Tolak Pengajuan Awal

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat memutuskan untuk menolak langsung berkas pengajuan reguler mahasiswa di awal (status **"Sudah Diajukan"**), misalnya karena berkas tidak memenuhi kelengkapan administratif.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/Kelola%20Daftar%20Pengajuan/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menolak Pengajuan Awal

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Mengklik Tolak, mengisi alasan penolakan, lalu klik Konfirmasi
    activate UI
    UI->>Ctrl: 2: rejectSubmission(id, rejection_reason)
    activate Ctrl
    Ctrl->>Model: 3: update(status = 'rejected', rejection_reason)
    activate Model
    Model-->>Ctrl: 4: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 5: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 6: Status Berubah menjadi 'Ditolak'
    deactivate UI
```
