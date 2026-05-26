# Sequence Diagram - Pantau Progres Penilaian

Diagram ini menggambarkan alur Ketua Program Studi (Kaprodi) saat memantau progres penilaian berkas yang berstatus **"Sedang Ditinjau"** di mana masih ada dosen penilai yang belum selesai menginput nilai mereka.

Kembali ke **[Diagram Utama Kelola Daftar Pengajuan](file:///opt/lampp/htdocs/ta_submission/docs/uml/Sequence%20Diagram/Kaprodi/sequence_diagram_kelola_daftar_pengajuan.md)**.

---

## 🎬 Diagram: Memantau Progres Penilaian

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Detail Pengajuan
    
    Kaprodi->>UI: 1: Memantau progres penilaian
    activate UI
    Note over UI: Status berkas: 'Sedang Ditinjau'<br/>(Masih ada dosen penilai yang belum selesai menilai)
    UI-->>Kaprodi: 2: Menampilkan pesan menunggu penilaian selesai
    deactivate UI
```
