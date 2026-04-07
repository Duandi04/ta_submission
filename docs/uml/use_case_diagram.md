# Use Case Diagram

Diagram ini menunjukkan interaksi antara aktor (pengguna sistem) dengan fungsi-fungsi utama.

```mermaid
usecaseDiagram
    actor Mahasiswa
    actor Dosen
    actor Kaprodi

    rectangle "Sistem Pengajuan Tugas Akhir" {
        %% Use cases for Mahasiswa
        usecase "Buat Pengajuan Draft Tugas Akhir" as UC1
        usecase "Unggah Dokumen (Proposal/Bab/Akhir)" as UC2
        usecase "Lihat Status & Komentar Bimbingan" as UC3
        usecase "Unggah Revisi Dokumen" as UC4

        %% Use cases for Dosen (Pembimbing / Penguji)
        usecase "Lihat Daftar Mahasiswa Bimbingan/Ujian" as UC5
        usecase "Melihat & Meninjau Dokumen Mahasiswa" as UC6
        usecase "Memberikan Komentar & Keputusan Revisi" as UC7
        usecase "Menyetujui Dokumen Mahasiswa" as UC8
        usecase "Memberikan Penilaian Sidang (Assessment)" as UC9

        %% Use cases for Kaprodi
        usecase "Pantau Seluruh Pengajuan" as UC10
        usecase "Menugaskan Dosen Pembimbing/Penguji" as UC11
        usecase "Menjadwalkan Tanggal Sidang" as UC12
    }

    Mahasiswa --> UC1
    Mahasiswa --> UC2
    Mahasiswa --> UC3
    Mahasiswa --> UC4

    Dosen --> UC5
    Dosen --> UC6
    Dosen --> UC7
    Dosen --> UC8
    Dosen --> UC9

    Kaprodi --> UC10
    Kaprodi --> UC11
    Kaprodi --> UC12

    UC9 ..> UC5 : <<include>>
    UC6 ..> UC5 : <<include>>
```
