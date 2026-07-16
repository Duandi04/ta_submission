# Sequence Diagram - Tolak Hasil Ujian

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat memutuskan untuk menolak kelulusan/ujian proposal mahasiswa berdasarkan hasil penilaian ujian yang diinput oleh dosen penguji/penilai (status berkas saat ini **"Sedang Ditinjau"**).

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/Kelola%20Daftar%20Pengajuan/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menolak Hasil Ujian Proposal

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
    Ctrl-->>UI: 5: Mengembalikan view detail pengajuan (dengan opsi tolak hasil)
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan seluruh nilai & opsi penolakan hasil ujian
    deactivate UI

    Kaprodi->>UI: 7: Mengklik Tolak, mengisi catatan tidak lulus/revisi, lalu klik Konfirmasi
    activate UI
    UI->>Ctrl: 8: rejectSubmission(id, rejection_reason)
    activate Ctrl
    Ctrl->>Model: 9: update(status = 'rejected', rejection_reason)
    activate Model
    Model-->>Ctrl: 10: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 11: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 12: Status Berubah menjadi 'Ditolak'
    deactivate UI
```
