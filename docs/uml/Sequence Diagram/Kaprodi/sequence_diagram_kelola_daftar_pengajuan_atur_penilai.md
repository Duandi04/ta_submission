# Sequence Diagram - Atur Dosen Penilai & Rubrik

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat menetapkan dosen penilai (penguji) dan rubrik penilaian untuk berkas pengajuan reguler mahasiswa baru (status **"Sudah Diajukan"**), yang menandai dimulainya proses review berkas.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Menetapkan Dosen Penilai & Rubrik

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Memilih Dosen Penilai & Rubrik Penilaian, lalu klik Simpan
    activate UI
    UI->>Ctrl: 2: assignLecturers(id, assessor_ids, rubric_id)
    activate Ctrl
    Ctrl->>Model: 3: update(assessor_ids, rubric_id, status = 'under_review')
    activate Model
    Model-->>Ctrl: 4: Data berhasil disimpan
    deactivate Model
    Ctrl-->>UI: 5: Redirect back dengan notifikasi sukses
    deactivate Ctrl
    UI-->>Kaprodi: 6: Status Berubah menjadi 'Sedang Ditinjau'
    deactivate UI
```
