# Sequence Diagram - Menilai Draft Proposal Mahasiswa

Diagram ini menunjukkan interaksi sistem saat Dosen memberikan penilaian (skor kriteria rubrik & komentar kelayakan) pada draft proposal tugas akhir mahasiswa.

```mermaid
sequenceDiagram
    actor Dosen as Lifeline1: Dosen
    participant UI as UI Penilaian Page
    participant Ctrl as Dosen\AssessmentController
    participant M_Assessment as Model Assessment
    participant M_Submission as Model ThesisSubmission

    Dosen->>UI: 1: Mengisi form penilaian (nilai rubrik, komentar) & klik kirim
    activate UI
    UI->>Ctrl: 2: store(request)
    activate Ctrl
    Ctrl->>Ctrl: 3: Validate assessment data
    
    alt Sukses (Validasi Berhasil)
        Ctrl->>M_Assessment: 4: create(assessmentData) & save scores
        activate M_Assessment
        M_Assessment-->>Ctrl: 5: Assessment Saved
        deactivate M_Assessment
        Ctrl->>M_Submission: 6: updateStatus() / calculateFinalScore()
        activate M_Submission
        M_Submission-->>Ctrl: 7: Status & Final Score Updated
        deactivate M_Submission
        Ctrl-->>UI: 8: Penilaian Berhasil Disimpan
        UI-->>Dosen: 9: Menampilkan Detail Proposal dengan Status Baru
    else Gagal (Validasi Gagal)
        Ctrl-->>UI: 10: Penilaian Gagal
        deactivate Ctrl
        UI-->>Dosen: 11: Menampilkan Pesan Error
    end
    deactivate UI
```
