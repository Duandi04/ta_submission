# Sequence Diagram - Kelola Detail Pengajuan

Diagram ini menunjukkan interaksi sistem saat Mahasiswa memperbarui atau mengelola detail pengajuan tugas akhir/proposal mereka (misalnya memperbarui judul, abstrak, atau mengunggah ulang file proposal).

```mermaid
sequenceDiagram
    actor Mahasiswa as Lifeline1: Mahasiswa
    participant UI as UI Detail Pengajuan Page
    participant Ctrl as Student\SubmissionController
    participant M_Submission as Model ThesisSubmission
    participant M_File as Model SubmissionFile

    Mahasiswa->>UI: 1: Mengisi form perubahan & unggah file proposal baru
    activate UI
    UI->>Ctrl: 2: update(request, submissionId)
    activate Ctrl
    Ctrl->>Ctrl: 3: Validate input & file
    
    alt Sukses (Validasi Berhasil)
        Ctrl->>M_Submission: 4: update(submissionData)
        activate M_Submission
        M_Submission-->>Ctrl: 5: Submission Updated
        deactivate M_Submission
        
        opt Ada File Baru Diunggah
            Ctrl->>M_File: 6: store(fileData) & delete old file
            activate M_File
            M_File-->>Ctrl: 7: File Saved
            deactivate M_File
        end
        
        Ctrl-->>UI: 8: Pengajuan Berhasil Diperbarui
        UI-->>Mahasiswa: 9: Menampilkan Pesan Sukses & Detail Terupdate
    else Gagal (Validasi Gagal)
        Ctrl-->>UI: 10: Pembaruan Gagal
        deactivate Ctrl
        UI-->>Mahasiswa: 11: Menampilkan Pesan Error
    end
    deactivate UI
```
