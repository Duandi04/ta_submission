# Sequence Diagram - Tambah Data Riwayat

Diagram ini menggambarkan alur tindakan mandiri Ketua Program Studi (Kaprodi) saat menambahkan data riwayat (historical data) skripsi/tugas akhir mahasiswa terdahulu ke dalam sistem.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/Kelola%20Daftar%20Pengajuan/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Tambah Data Riwayat Mandiri

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Pengajuan / Form History
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    Kaprodi->>UI: 1: Klik tombol "Tambah Data History"
    activate UI
    UI->>Ctrl: 2: createHistorical()
    activate Ctrl
    Ctrl-->>UI: 3: Mengembalikan View Form Tambah Data History
    deactivate Ctrl
    UI-->>Kaprodi: 4: Menampilkan Form Tambah Data History
    deactivate UI

    Kaprodi->>UI: 5: Mengisi detail data history pengajuan & klik simpan
    activate UI
    UI->>Ctrl: 6: store(title, abstract, student_id, is_historical = true)
    activate Ctrl
    Ctrl->>Model: 7: create(title, abstract, student_id, is_historical = true)
    activate Model
    Model-->>Ctrl: 8: Historical Record Created
    deactivate Model
    Ctrl-->>UI: 9: Redirect ke daftar pengajuan
    deactivate Ctrl
    UI-->>Kaprodi: 10: Data History Pengajuan Berhasil Disimpan & Tampil di Tabel
    deactivate UI
```
