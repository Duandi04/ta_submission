# Sequence Diagram - Kelola Dosen (Read, Create, Update, Import, Export)

Diagram ini menunjukkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola data dosen, meliputi proses melihat daftar & detail (Read), tambah (Create), edit (Update), impor massal dari file (Import), dan ekspor data ke Excel (Export). Langkah awal membuka halaman diletakkan di luar percabangan agar runtun secara logis.

```mermaid
sequenceDiagram
    actor Kaprodi as Lifeline1: Kaprodi
    participant UI as UI Kelola Dosen Page
    participant Ctrl as Kaprodi\LecturerController
    participant Model as Model User
    participant Exc as Maatwebsite\Excel

    %% ==========================================
    %% TAHAP AWAL: MEMBUKA HALAMAN KELOLA DOSEN (COMMON STEP)
    %% ==========================================
    Kaprodi->>UI: 1: Membuka Halaman Kelola Dosen
    activate UI
    UI->>Ctrl: 2: index(Request)
    activate Ctrl
    Ctrl->>Model: 3: Query data dosen (role dosen & prodi_id)
    activate Model
    Model-->>Ctrl: 4: Data Dosen
    deactivate Model
    Ctrl-->>UI: 5: Render Halaman Daftar Dosen
    deactivate Ctrl
    UI-->>Kaprodi: 6: Menampilkan Halaman Daftar Dosen & Pilihan Aksi
    deactivate UI

    %% ==========================================
    %% TAHAP LANJUTAN: PILIHAN TINDAKAN (ALT BLOCK)
    %% ==========================================
    alt Aksi: Tambah Dosen Baru (Create)
        Kaprodi->>UI: 7: Mengisi form dosen baru & klik simpan
        activate UI
        UI->>Ctrl: 8: store(UserRequest)
        activate Ctrl
        Ctrl->>Model: 9: User::create(userData) & assignRole('dosen')
        activate Model
        Model-->>Ctrl: 10: User Created & Role Assigned
        deactivate Model
        Ctrl-->>UI: 11: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12: Menampilkan Dosen Baru di Daftar
        deactivate UI

    else Aksi: Ubah Data Dosen (Update)
        Kaprodi->>UI: 7: Mengubah data dosen & klik perbarui
        activate UI
        UI->>Ctrl: 8: update(UserRequest, lecturerId)
        activate Ctrl
        Ctrl->>Model: 9: $lecturer->update(userData) & syncRoles()
        activate Model
        Model-->>Ctrl: 10: User Updated
        deactivate Model
        Ctrl-->>UI: 11: Redirect dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 12: Menampilkan Data Terupdate di Daftar
        deactivate UI

    else Aksi: Impor Dosen Dari Excel (Import)
        Kaprodi->>UI: 7: Mengunggah file Excel/CSV & klik import
        activate UI
        UI->>Ctrl: 8: import(Request)
        activate Ctrl
        Ctrl->>Exc: 9: Excel::import(new UserImport('dosen'), file)
        activate Exc
        Exc->>Model: 10: Insert multiple dosen
        activate Model
        Model-->>Exc: 11: Data Stored
        deactivate Model
        Exc-->>Ctrl: 12: Import Success
        deactivate Exc
        Ctrl-->>UI: 13: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>Kaprodi: 14: Menampilkan Daftar Dosen Hasil Import
        deactivate UI

    else Aksi: Ekspor Dosen Ke Excel (Export)
        Kaprodi->>UI: 7: Mengklik tombol Export Dosen
        activate UI
        UI->>Ctrl: 8: export(Request)
        activate Ctrl
        Ctrl->>Exc: 9: Excel::download(new UserExport('dosen'), filename)
        activate Exc
        Exc-->>Ctrl: 10: Stream File Download (.xlsx)
        deactivate Exc
        Ctrl-->>UI: 11: Return Download Response
        deactivate Ctrl
        UI-->>Kaprodi: 12: File Excel Terunduh Otomatis
        deactivate UI
    end
```
