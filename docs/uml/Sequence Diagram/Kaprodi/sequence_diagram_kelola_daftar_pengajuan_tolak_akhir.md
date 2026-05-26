# Sequence Diagram - Tolak Hasil Ujian

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat memutuskan untuk menolak kelulusan/ujian proposal mahasiswa berdasarkan hasil penilaian ujian yang diinput oleh dosen penguji/penilai (status berkas saat ini **"Sedang Ditinjau"**).

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menolak Hasil Ujian Proposal

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Note over Kaprodi, Model: Status berkas: 'Sedang Ditinjau' (Seluruh dosen penilai telah menginput nilai)

    Kaprodi->>UI: 1: Membuka detail pengajuan dengan seluruh nilai lengkap
    activate UI
    UI-->>Kaprodi: 2: Menampilkan seluruh nilai & opsi penolakan hasil ujian
    deactivate UI

    Kaprodi->>UI: 3: Mengklik Tolak, mengisi catatan tidak lulus/revisi, lalu klik Konfirmasi
    activate UI
    UI->>Ctrl: 4: rejectSubmission(id, rejection_reason)
    activate Ctrl
    Ctrl->>Model: 5: update(status = 'rejected', rejection_reason)
    activate Model
    Model-->>Ctrl: 6: Data berhasil diperbarui
    deactivate Model
    Ctrl-->>UI: 7: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 8: Status Berubah menjadi 'Ditolak'
    deactivate UI
```
