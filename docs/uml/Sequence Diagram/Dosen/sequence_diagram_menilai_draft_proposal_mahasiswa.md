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
    Ctrl-->>UI_List: 5: Menampilkan Daftar Proposal
    deactivate Ctrl

    Dosen->>UI_List: 6: Klik "Edit/Nilai" pada proposal berstatus "Draft" (atau belum dinilai)
    UI_List->>Ctrl: 7: edit(submissionId)
    activate Ctrl
    Ctrl->>M_Submission: 8: findWithRubric(submissionId)
    activate M_Submission
    M_Submission-->>Ctrl: 9: Data Proposal & Rubrik Penilaian
    deactivate M_Submission
    Ctrl-->>UI_Form: 10: Menampilkan Halaman Form Penilaian & Komentar
    deactivate Ctrl
    activate UI_Form
    deactivate UI_List

    Dosen->>UI_Form: 11: Mengisi skor pada Rubrik Penilaian & menulis Komentar kelayakan

    alt Pilihan A: Klik "Perbarui Draft Penilaian" (Simpan Sementara)
        Dosen->>UI_Form: 12a: Klik "Perbarui Draft Penilaian"
        UI_Form->>Ctrl: 13a: saveDraft(scores, comment, submissionId)
        activate Ctrl
        Ctrl->>M_Assessment: 14a: updateOrCreateDraft(scores, comment)
        activate M_Assessment
        M_Assessment-->>Ctrl: 15a: Draft Penilaian Disimpan
        deactivate M_Assessment
        Ctrl-->>UI_Form: 16a: Menampilkan pesan "Draft berhasil diperbarui"
        deactivate Ctrl

    else Pilihan B: Klik "Kirim Penilaian Final" (Kirim Permanen)
        Dosen->>UI_Form: 12b: Klik "Kirim Penilaian Final"
        UI_Form->>Ctrl: 13b: submitFinal(scores, comment, submissionId)
        activate Ctrl
        Ctrl->>M_Assessment: 14b: saveFinalAssessment(scores, comment)
        activate M_Assessment
        M_Assessment-->>Ctrl: 15b: Penilaian Final Disimpan
        deactivate M_Assessment
        Ctrl->>M_Submission: 16b: updateStatusToSubmitted()
        activate M_Submission
        M_Submission-->>Ctrl: 17b: Status Pengajuan Diperbarui menjadi "sudah submit"
        deactivate M_Submission
        Ctrl-->>UI_List: 18b: Redirect ke Daftar Review dengan Pesan Sukses
        activate UI_List
        UI_List-->>Dosen: 19b: Menampilkan Daftar Review Terupdate
        deactivate UI_List
        deactivate Ctrl
    end
    deactivate UI_Form
```
