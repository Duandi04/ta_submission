# Sequence Diagram - Lihat Proposal Mahasiswa Bimbingan

Diagram ini menunjukkan interaksi sistem saat Dosen melihat daftar pengajuan tugas akhir/proposal dari mahasiswa bimbingannya.

```mermaid
sequenceDiagram
    actor Dosen as Lifeline1: Dosen
    participant UI_Bimbingan as UI Daftar Mahasiswa Bimbingan
    participant UI_Drafts as UI Daftar Draft Mahasiswa
    participant UI_Detail as UI Detail Proposal Diterima
    participant Ctrl as Dosen\BimbinganController
    participant M_Student as Model User (Mahasiswa)
    participant M_Submission as Model ThesisSubmission

    Dosen->>UI_Bimbingan: 1: Membuka Halaman Bimbingan Mahasiswa
    activate UI_Bimbingan
    UI_Bimbingan->>Ctrl: 2: index() / bimbingan()
    activate Ctrl
    Ctrl->>M_Student: 3: getStudentsBySupervisor(dosen_id)
    activate M_Student
    M_Student-->>Ctrl: 4: Daftar Mahasiswa Bimbingan
    deactivate M_Student
    Ctrl-->>UI_Bimbingan: 5: Menampilkan Daftar Mahasiswa Bimbingan
    deactivate Ctrl

    Dosen->>UI_Bimbingan: 6: Klik "Draft" pada data mahasiswa pilihan
    UI_Bimbingan->>Ctrl: 7: getStudentDrafts(studentId)
    activate Ctrl
    Ctrl->>M_Submission: 8: getSubmissionsByStudent(studentId)
    activate M_Submission
    M_Submission-->>Ctrl: 9: Daftar Draft/Proposal Mahasiswa
    deactivate M_Submission
    Ctrl-->>UI_Drafts: 10: Menampilkan Daftar Proposal/Draft Mahasiswa
    deactivate Ctrl
    activate UI_Drafts
    deactivate UI_Bimbingan

    Dosen->>UI_Drafts: 11: Klik "Detail" pada proposal yang berstatus "Diterima"
    UI_Drafts->>Ctrl: 12: showProposalDetail(submissionId)
    activate Ctrl
    Ctrl->>M_Submission: 13: findOrFail(submissionId)
    activate M_Submission
    M_Submission-->>Ctrl: 14: Data Lengkap Proposal Diterima
    deactivate M_Submission
    Ctrl-->>UI_Detail: 15: Menampilkan Halaman Detail Proposal
    deactivate Ctrl
    activate UI_Detail
    UI_Detail-->>Dosen: 16: Selesai melihat detail proposal bimbingan
    deactivate UI_Detail
    deactivate UI_Drafts
```
