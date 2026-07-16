# Sequence Diagram - Terima Pengajuan & Atur Pembimbing

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat menyetujui proposal tugas akhir mahasiswa setelah seluruh dosen penilai selesai memberikan nilai ujian, serta menetapkan Dosen Pembimbing 1 dan Dosen Pembimbing 2 aktif.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/Kelola%20Daftar%20Pengajuan/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menerima Pengajuan & Menetapkan Pembimbing

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Membuka halaman detail pengajuan
    activate UI
    UI->>Ctrl: 2: submissionShow(id)
    activate Ctrl
    Ctrl->>Model: 3: with(['student', 'assessments.scores'])->findOrFail(id)
    activate Model
    Model-->>Ctrl: 4: Data pengajuan & penilaian lengkap
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan view detail pengajuan (dengan form Dosen Pembimbing)
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan detail & form penunjukan Dosen Pembimbing
    deactivate UI

    Kaprodi->>UI: 7: Memilih Dosen Pembimbing 1 & 2, lalu klik Terima Pengajuan
    activate UI
    UI->>Ctrl: 8: acceptSubmission(id, supervisor_id, supervisor_2_id)
    activate Ctrl
    Ctrl->>Model: 9: update(status = 'approved', supervisor_id, supervisor_2_id)
    activate Model
    Model-->>Ctrl: 10: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 11: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 12: Status Berubah menjadi 'Disetujui'
    deactivate UI
```
