# Sequence Diagram - Kelola Detail Pengajuan

Diagram ini menunjukkan interaksi sistem saat Mahasiswa memperbarui atau mengelola detail pengajuan tugas akhir/proposal mereka (misalnya memperbarui judul, abstrak, atau mengunggah ulang file proposal).

```mermaid
sequenceDiagram
    actor Mahasiswa as Lifeline1: Mahasiswa
    participant UI_List as UI Daftar Pengajuan
    participant UI_Form as UI Form Pengajuan Baru
    participant UI_Detail as UI Detail Pengajuan (Draft)
    participant Ctrl as Student\SubmissionController
    participant M_Submission as Model ThesisSubmission
    participant M_File as Model SubmissionFile

    %% TAHAP AWAL: MEMBUKA DAFTAR PENGAJUAN
    Mahasiswa->>UI_List: 1: Membuka Menu Daftar Pengajuan
    activate UI_List
    UI_List->>Ctrl: 2: index()
    activate Ctrl
    Ctrl->>M_Submission: 3: getSubmissionsByStudent(student_id)
    activate M_Submission
    M_Submission-->>Ctrl: 4: Daftar Pengajuan
    deactivate M_Submission
    Ctrl-->>UI_List: 5: Menampilkan Daftar Pengajuan
    deactivate Ctrl

    %% ALIRAN PILIHAN TINDAKAN
    alt Aksi: Membuat Pengajuan Proposal Baru (Create)
        Mahasiswa->>UI_List: 6a: Klik "Buat Pengajuan Baru"
        UI_List->>Ctrl: 7a: create()
        activate Ctrl
        Ctrl-->>UI_Form: 8a: Menampilkan Form Pengajuan Baru
        deactivate Ctrl
        activate UI_Form
        deactivate UI_List

        Mahasiswa->>UI_Form: 9a: Mengisi judul, abstrak, unggah file, & klik "Kirim"
        UI_Form->>Ctrl: 10a: store(request)
        activate Ctrl
        Ctrl->>Ctrl: 11a: Validasi input & berkas
        Ctrl->>M_Submission: 12a: create(submissionData)
        activate M_Submission
        M_Submission-->>Ctrl: 13a: Proposal Baru Disimpan
        deactivate M_Submission
        Ctrl->>M_File: 14a: storeFile(fileData)
        activate M_File
        M_File-->>Ctrl: 15a: File Berhasil Disimpan
        deactivate M_File
        Ctrl-->>UI_List: 16a: Redirect ke Daftar Pengajuan dengan Pesan Sukses
        activate UI_List
        UI_List-->>Mahasiswa: 17a: Proposal baru tampil di tabel dengan status "Draft" / "Diajukan"
        deactivate UI_List
        deactivate Ctrl
        deactivate UI_Form

    else Aksi: Kelola Pengajuan Berstatus "Draft" via Halaman Detail
        Mahasiswa->>UI_List: 6b: Klik "Detail" pada pengajuan berstatus "Draft"
        activate UI_List
        UI_List->>Ctrl: 7b: show(submissionId)
        activate Ctrl
        Ctrl->>M_Submission: 8b: findOrFail(submissionId)
        activate M_Submission
        M_Submission-->>Ctrl: 9b: Detail Data Draft Proposal
        deactivate M_Submission
        Ctrl-->>UI_Detail: 10b: Menampilkan Halaman Detail Pengajuan Draft
        deactivate Ctrl
        activate UI_Detail
        deactivate UI_List

        alt Sub-Aksi 1: Klik "Ajukan Sekarang" (Kirim Pengajuan)
            Mahasiswa->>UI_Detail: 11b: Klik tombol "Ajukan Sekarang"
            UI_Detail->>Ctrl: 12b: submitProposal(submissionId)
            activate Ctrl
            Ctrl->>M_Submission: 13b: update(status = 'submitted')
            activate M_Submission
            M_Submission-->>Ctrl: 14b: Status Pengajuan Berubah
            deactivate M_Submission
            Ctrl-->>UI_List: 15b: Redirect ke Daftar Pengajuan dengan Notifikasi Sukses
            activate UI_List
            UI_List-->>Mahasiswa: 16b: Menampilkan Status Terbaru "Diajukan" di Tabel
            deactivate UI_List
            deactivate Ctrl

        else Sub-Aksi 2: Klik "Batalkan Pengajuan" (Hapus Draft)
            Mahasiswa->>UI_Detail: 11c: Klik tombol "Batalkan Pengajuan"
            UI_Detail->>Ctrl: 12c: destroy(submissionId)
            activate Ctrl
            Ctrl->>M_File: 13c: deleteAssociatedFiles(submissionId)
            activate M_File
            M_File-->>Ctrl: 14c: File Terhapus
            deactivate M_File
            Ctrl->>M_Submission: 15c: delete(submissionId)
            activate M_Submission
            M_Submission-->>Ctrl: 16c: Data Pengajuan Dihapus dari DB
            deactivate M_Submission
            Ctrl-->>UI_List: 17c: Redirect ke Daftar Pengajuan dengan Notifikasi Penghapusan
            activate UI_List
            UI_List-->>Mahasiswa: 18c: Pengajuan terhapus dari tabel daftar
            deactivate UI_List
            deactivate Ctrl
        end
        deactivate UI_Detail
    end
```
