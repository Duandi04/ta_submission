# Sequence Diagram - Kelola Daftar Pengajuan

Diagram ini menggambarkan interaksi sistem saat Ketua Program Studi (Kaprodi) mengelola daftar pengajuan proposal tugas akhir mahasiswa.

```mermaid
sequenceDiagram
    actor Kaprodi as Kaprodi
    participant UI as Halaman Kelola Pengajuan
    participant Ctrl as KaprodiController
    participant Model as Model ThesisSubmission

    %% 1. VIEW LIST OF SUBMISSIONS
    Kaprodi->>UI: 1: Membuka Halaman Daftar Pengajuan
    activate UI
    UI-->>Kaprodi: 2: Menampilkan Tabel Daftar Pengajuan Mahasiswa
    deactivate UI

    %% 2. VIEW DETAILED SUBMISSION
    Kaprodi->>UI: 3: Mengklik Lihat Detail salah satu pengajuan
    activate UI
    UI-->>Kaprodi: 4: Menampilkan Detail Pengajuan & Pilihan Tindakan
    deactivate UI

    %% CONDITIONAL FLOW BASED ON STATE
    alt [Jika Berkas adalah Data History/Riwayat]
        
        Kaprodi->>UI: 5a: Klik tombol "Edit History"
        activate UI
        UI->>Ctrl: 6a: editHistorical(id)
        activate Ctrl
        Ctrl->>Model: 7a: findOrFail(id)
        activate Model
        Model-->>Ctrl: 8a: Data History
        deactivate Model
        Ctrl-->>UI: 9a: Mengembalikan View Form Edit History
        deactivate Ctrl
        UI-->>Kaprodi: 10a: Menampilkan Form Edit History
        deactivate UI

        Kaprodi->>UI: 11a: Mengisi rincian data history baru & klik perbarui
        activate UI
        UI->>Ctrl: 12a: updateHistorical(id, title, abstract, student_id)
        activate Ctrl
        Ctrl->>Model: 13a: update(id, title, abstract, student_id)
        activate Model
        Model-->>Ctrl: 14a: Data berhasil diperbarui
        deactivate Model
        Ctrl-->>UI: 15a: Redirect ke detail dengan notifikasi sukses
        deactivate Ctrl
        UI-->>Kaprodi: 16a: Data History Pengajuan Berhasil Diperbarui
        deactivate UI

    else [Jika Berkas adalah Pengajuan Reguler]
        
        alt [Jika Status Berkas adalah 'Sudah Diajukan']
            
            alt Tindakan: Simpan Dosen Penilai & Rubrik
                Kaprodi->>UI: 5b: Memilih Dosen Penilai & Rubrik Penilaian, lalu klik Simpan
                activate UI
                UI->>Ctrl: 6b: assignLecturers(id, assessor_ids, rubric_id)
                activate Ctrl
                Ctrl->>Model: 7b: update(assessor_ids, rubric_id, status = 'under_review')
                activate Model
                Model-->>Ctrl: 8b: Data berhasil disimpan
                deactivate Model
                Ctrl-->>UI: 9b: Redirect back dengan notifikasi sukses
                deactivate Ctrl
                UI-->>Kaprodi: 10b: Status Berubah menjadi 'Sedang Ditinjau'
                deactivate UI
                
            else Tindakan: Tolak Pengajuan Awal
                Kaprodi->>UI: 5b: Mengklik Tolak, mengisi alasan penolakan, lalu klik Konfirmasi
                activate UI
                UI->>Ctrl: 6b: rejectSubmission(id, rejection_reason)
                activate Ctrl
                Ctrl->>Model: 7b: update(status = 'rejected', rejection_reason)
                activate Model
                Model-->>Ctrl: 8b: Data berhasil diperbarui
                deactivate Model
                Ctrl-->>UI: 9b: Redirect back dengan notifikasi sukses
                deactivate Ctrl
                UI-->>Kaprodi: 10b: Status Berubah menjadi 'Ditolak'
                deactivate UI
            end

        else [Jika Status Berkas adalah 'Sedang Ditinjau']
            
            alt [Jika masih ada dosen penilai yang belum memberikan nilai]
                Kaprodi->>UI: 5b: Memantau progres penilaian
                activate UI
                UI-->>Kaprodi: 6b: Menampilkan pesan menunggu penilaian selesai
                deactivate UI
 
            else [Jika semua dosen penilai sudah memberikan nilai]
                Kaprodi->>UI: 5b: Membuka detail pengajuan dengan seluruh nilai lengkap
                activate UI
                UI-->>Kaprodi: 6b: Form Atur Dosen Pembimbing Aktif
                deactivate UI
                
                alt Tindakan: Terima Pengajuan & Tetapkan Pembimbing
                    Kaprodi->>UI: 7b: Memilih Dosen Pembimbing 1 & 2, lalu klik Terima Pengajuan
                    activate UI
                    UI->>Ctrl: 8b: acceptSubmission(id, supervisor_id, supervisor_2_id)
                    activate Ctrl
                    Ctrl->>Model: 9b: update(status = 'approved', supervisor_id, supervisor_2_id)
                    activate Model
                    Model-->>Ctrl: 10b: Data berhasil diperbarui
                    deactivate Model
                    Ctrl-->>UI: 11b: Redirect back dengan notifikasi sukses
                    deactivate Ctrl
                    UI-->>Kaprodi: 12b: Status Berubah menjadi 'Disetujui'
                    deactivate UI
                    
                else Tindakan: Tolak Hasil Ujian
                    Kaprodi->>UI: 7b: Mengklik Tolak, mengisi catatan tidak lulus/revisi, lalu klik Konfirmasi
                    activate UI
                    UI->>Ctrl: 8b: rejectSubmission(id, rejection_reason)
                    activate Ctrl
                    Ctrl->>Model: 9b: update(status = 'rejected', rejection_reason)
                    activate Model
                    Model-->>Ctrl: 10b: Data berhasil diperbarui
                    deactivate Model
                    Ctrl-->>UI: 11b: Redirect back dengan notifikasi sukses
                    deactivate Ctrl
                    UI-->>Kaprodi: 12b: Status Berubah menjadi 'Ditolak'
                    deactivate UI
                end
            end
        end
    end

    %% 3. INDEPENDENT ACTION: ADD HISTORICAL DATA
    opt Tindakan Mandiri: Tambah Data History Pengajuan
        Kaprodi->>UI: 13: Klik tombol "Tambah Data History"
        activate UI
        UI->>Ctrl: 14: createHistorical()
        activate Ctrl
        Ctrl-->>UI: 15: Mengembalikan View Form Tambah Data History
        deactivate Ctrl
        UI-->>Kaprodi: 16: Menampilkan Form Tambah Data History
        deactivate UI

        Kaprodi->>UI: 17: Mengisi detail data history pengajuan & klik simpan
        activate UI
        UI->>Ctrl: 18: store(title, abstract, student_id, is_historical = true)
        activate Ctrl
        Ctrl->>Model: 19: create(title, abstract, student_id, is_historical = true)
        activate Model
        Model-->>Ctrl: 20: Historical Record Created
        deactivate Model
        Ctrl-->>UI: 21: Redirect ke daftar pengajuan
        deactivate Ctrl
        UI-->>Kaprodi: 22: Data History Pengajuan Berhasil Disimpan & Tampil di Tabel
        deactivate UI
    end
```
