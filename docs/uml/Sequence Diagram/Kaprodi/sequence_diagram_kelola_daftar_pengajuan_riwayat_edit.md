# Sequence Diagram - Edit Data Riwayat

Diagram ini menggambarkan alur tindakan Ketua Program Studi (Kaprodi) saat melakukan pengubahan (edit) pada data riwayat (historical data) skripsi/tugas akhir mahasiswa yang sudah ada di dalam sistem.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Edit Data Riwayat

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail / Form Edit History
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Klik tombol "Edit History"
    activate UI
    UI->>Ctrl: 2: editHistorical(id)
    activate Ctrl
    Ctrl->>Model: 3: findOrFail(id)
    activate Model
    Model-->>Ctrl: 4: Data History
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Form Edit History
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Form Edit History
    deactivate UI

    Kaprodi->>UI: 7: Mengisi rincian data history baru & klik perbarui
    activate UI
    UI->>Ctrl: 8: updateHistorical(id, title, abstract, student_id)
    activate Ctrl
    Ctrl->>Model: 9: update(id, title, abstract, student_id)
    activate Model
    Model-->>Ctrl: 10: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 11: Redirect ke detail dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 12: Data History Pengajuan Berhasil Diperbarui
    deactivate UI
```
