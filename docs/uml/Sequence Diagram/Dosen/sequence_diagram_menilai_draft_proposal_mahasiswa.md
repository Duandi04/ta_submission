# Sequence Diagram - Menilai Draft Proposal Mahasiswa

Diagram ini menunjukkan interaksi sistem saat Dosen memberikan penilaian (skor kriteria rubrik & komentar kelayakan) pada draft proposal tugas akhir mahasiswa.

```mermaid
sequenceDiagram
    actor Dosen as Lifeline1: Dosen
    participant UI_List as UI Daftar Review Proposal
    participant UI_Form as UI Form Penilaian (Rubrik & Komentar)
    participant Ctrl as Dosen\AssessmentController
    participant M_Assessment as Model Assessment
    participant M_Submission as Model ThesisSubmission

    Dosen->>UI_List: 1: Membuka Halaman Review Proposal Mahasiswa
    activate UI_List
    UI_List->>Ctrl: 2: index()
    activate Ctrl
    Ctrl->>M_Submission: 3: getSubmissionsForReview(dosen_id)
    activate M_Submission
    M_Submission-->>Ctrl: 4: Daftar Proposal Mahasiswa
    deactivate M_Submission
    Ctrl-->>UI_List: 5: Mengembalikan View Daftar Proposal
    deactivate Ctrl
    UI_List-->>Dosen: 6: Menampilkan Daftar Proposal
    deactivate UI_List

    Dosen->>UI_List: 7: Klik "Edit/Nilai" pada proposal berstatus "Draft" (atau belum dinilai)
    activate UI_List
    UI_List->>Ctrl: 8: edit(submissionId)
    activate Ctrl
    Ctrl->>M_Submission: 9: findWithRubric(submissionId)
    activate M_Submission
    M_Submission-->>Ctrl: 10: Data Proposal & Rubrik Penilaian
    deactivate M_Submission
    Ctrl-->>UI_Form: 11: Mengembalikan View Form Penilaian & Komentar
    deactivate Ctrl
    activate UI_Form
    deactivate UI_List
    UI_Form-->>Dosen: 12: Menampilkan Halaman Form Penilaian & Komentar

    Dosen->>UI_Form: 13: Mengisi skor pada Rubrik Penilaian & menulis Komentar kelayakan

    alt Pilihan A: Klik "Perbarui Draft Penilaian" (Simpan Sementara)
        Dosen->>UI_Form: 14a: Klik "Perbarui Draft Penilaian"
        UI_Form->>Ctrl: 15a: saveDraft(scores, comment, submissionId)
        activate Ctrl
        Ctrl->>M_Assessment: 16a: updateOrCreateDraft(scores, comment)
        activate M_Assessment
        M_Assessment-->>Ctrl: 17a: Draft Penilaian Disimpan
        deactivate M_Assessment
        Ctrl-->>UI_Form: 18a: Mengembalikan pesan sukses
        deactivate Ctrl
        UI_Form-->>Dosen: 19a: Menampilkan pesan "Draft berhasil diperbarui"

    else Pilihan B: Klik "Kirim Penilaian Final" (Kirim Permanen)
        Dosen->>UI_Form: 14b: Klik "Kirim Penilaian Final"
        UI_Form->>Ctrl: 15b: submitFinal(scores, comment, submissionId)
        activate Ctrl
        Ctrl->>M_Assessment: 16b: saveFinalAssessment(scores, comment)
        activate M_Assessment
        M_Assessment-->>Ctrl: 17b: Penilaian Final Disimpan
        deactivate M_Assessment
        Ctrl->>M_Submission: 18b: updateStatusToSubmitted()
        activate M_Submission
        M_Submission-->>Ctrl: 19b: Status Pengajuan Diperbarui menjadi "sudah submit"
        deactivate M_Submission
        Ctrl-->>UI_List: 20b: Redirect ke Daftar Review dengan Pesan Sukses
        deactivate UI_Form
        activate UI_List
        UI_List-->>Dosen: 21b: Menampilkan Daftar Review Terupdate
        deactivate UI_List
        deactivate Ctrl
    end
```
