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
    deactivate UI_List

    %% ALIRAN PILIHAN TINDAKAN
    alt Aksi: Membuat Pengajuan Proposal Baru (Create)
        Mahasiswa->>UI_List: 6a: Klik "Buat Pengajuan Baru"
        activate UI_List
        UI_List->>Ctrl: 7a: create()
        activate Ctrl
        Ctrl-->>UI_Form: 8a: Mengembalikan View Form Pengajuan Baru
        deactivate Ctrl
        activate UI_Form
        deactivate UI_List
        UI_Form-->>Mahasiswa: 9a: Menampilkan Form Pengajuan Baru
 
        Mahasiswa->>UI_Form: 10a: Mengisi judul, abstrak, unggah file, & klik "Kirim"
        UI_Form->>Ctrl: 11a: store(request)
        activate Ctrl
        Ctrl->>Ctrl: 12a: Validasi input & berkas
        Ctrl->>M_Submission: 13a: create(submissionData)
        activate M_Submission
        M_Submission-->>Ctrl: 14a: Proposal Baru Disimpan
        deactivate M_Submission
        Ctrl->>M_File: 15a: storeFile(fileData)
        activate M_File
        M_File-->>Ctrl: 16a: File Berhasil Disimpan
        deactivate M_File
        Ctrl-->>UI_List: 17a: Redirect ke Daftar Pengajuan dengan Pesan Sukses
        deactivate UI_Form
        activate UI_List
        UI_List-->>Mahasiswa: 18a: Proposal baru tampil di tabel dengan status "Draft" / "Diajukan"
        deactivate UI_List
        deactivate Ctrl

    else Aksi: Kelola Pengajuan Berstatus "Draft" via Halaman Detail
        Mahasiswa->>UI_List: 6b: Klik "Detail" pada pengajuan berstatus "Draft"
        activate UI_List
        UI_List->>Ctrl: 7b: show(submissionId)
        activate Ctrl
        Ctrl->>M_Submission: 8b: findOrFail(submissionId)
        activate M_Submission
        M_Submission-->>Ctrl: 9b: Detail Data Draft Proposal
        deactivate M_Submission
        Ctrl-->>UI_Detail: 10b: Mengembalikan View Detail Pengajuan Draft
        deactivate Ctrl
        activate UI_Detail
        deactivate UI_List
        UI_Detail-->>Mahasiswa: 11b: Menampilkan Halaman Detail Pengajuan Draft
 
        alt Sub-Aksi 1: Klik "Ajukan Sekarang" (Kirim Pengajuan)
            Mahasiswa->>UI_Detail: 12b: Klik tombol "Ajukan Sekarang"
            UI_Detail->>Ctrl: 13b: submitProposal(submissionId)
            activate Ctrl
            Ctrl->>M_Submission: 14b: update(status = 'submitted')
            activate M_Submission
            M_Submission-->>Ctrl: 15b: Status Pengajuan Berubah
            deactivate M_Submission
            Ctrl-->>UI_List: 16b: Redirect ke Daftar Pengajuan dengan Notifikasi Sukses
            activate UI_List
            UI_List-->>Mahasiswa: 17b: Menampilkan Status Terbaru "Diajukan" di Tabel
            deactivate UI_List
            deactivate Ctrl
 
        else Sub-Aksi 2: Klik "Batalkan Pengajuan" (Hapus Draft)
            Mahasiswa->>UI_Detail: 12b: Klik tombol "Batalkan Pengajuan"
            UI_Detail->>Ctrl: 13b: destroy(submissionId)
            activate Ctrl
            Ctrl->>M_File: 14b: deleteAssociatedFiles(submissionId)
            activate M_File
            M_File-->>Ctrl: 15b: File Terhapus
            deactivate M_File
            Ctrl->>M_Submission: 16b: delete(submissionId)
            activate M_Submission
            M_Submission-->>Ctrl: 17b: Data Pengajuan Dihapus dari DB
            deactivate M_Submission
            Ctrl-->>UI_List: 18b: Redirect ke Daftar Pengajuan dengan Notifikasi Penghapusan
            activate UI_List
            UI_List-->>Mahasiswa: 19b: Pengajuan terhapus dari tabel daftar
            deactivate UI_List
            deactivate Ctrl
        end
        deactivate UI_Detail
    end
```
