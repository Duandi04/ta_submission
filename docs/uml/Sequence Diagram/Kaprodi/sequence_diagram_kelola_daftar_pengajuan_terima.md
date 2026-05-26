# Sequence Diagram - Terima Pengajuan & Atur Pembimbing

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat menyetujui proposal tugas akhir mahasiswa setelah seluruh dosen penilai selesai memberikan nilai ujian, serta menetapkan Dosen Pembimbing 1 dan Dosen Pembimbing 2 aktif.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menerima Pengajuan & Menetapkan Pembimbing

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Note over Kaprodi, Model: Status berkas: 'Sedang Ditinjau' (Seluruh dosen penilai telah menginput nilai)

    Kaprodi->>UI: 1: Membuka detail pengajuan dengan seluruh nilai lengkap
    activate UI
    UI-->>Kaprodi: 2: Menampilkan form penunjukan Dosen Pembimbing Aktif
    deactivate UI

    Kaprodi->>UI: 3: Memilih Dosen Pembimbing 1 & 2, lalu klik Terima Pengajuan
    activate UI
    UI->>Ctrl: 4: acceptSubmission(id, supervisor_id, supervisor_2_id)
    activate Ctrl
    Ctrl->>Model: 5: update(status = 'approved', supervisor_id, supervisor_2_id)
    activate Model
    Model-->>Ctrl: 6: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 7: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 8: Status Berubah menjadi 'Disetujui'
    deactivate UI
```
