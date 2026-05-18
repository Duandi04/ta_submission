# Class Diagram

Diagram Kelas (*Class Diagram*) ini menggambarkan struktur statis sistem dengan menunjukkan kelas-kelas yang ada (beserta atribut dan metodenya) serta hubungan antarkelas (Generalisasi/Pewarisan, Asosiasi, dan Komposisi). Diagram ini diselaraskan secara **presisi 100% dengan model Eloquent Laravel dan skema database aktual** (seperti `ThesisSubmission`, `Assessment`, dll.), serta disederhanakan dengan menyatukan berkas lampiran langsung ke kelas pengajuan utama demi kemudahan pemahaman akademis.

```mermaid
classDiagram
    %% ==========================================
    %% DEFINISI KELAS UTAMA (SESUAI LARAVEL MODEL)
    %% ==========================================
    class User {
        +String name
        +String email
        +String password
        +String nim_nip
        +String phone
        +String address
        +String profile_photo
        +Boolean is_active
        +Integer program_studi_id
    }

    class Mahasiswa {
        +Integer angkatan
        +Boolean can_exceed_submission_limit
        +buatPengajuanDraft(title, abstract, research_field, file)
        +submitPengajuanFinal(submission)
        +batalkanPengajuan(submission)
    }

    class Dosen {
        +lihatDaftarBimbingan()
        +buatPenilaianDraft(submission, score, comment)
        +submitPenilaianFinal(assessment)
    }

    class Kaprodi {
        +kelolaMahasiswa()
        +kelolaDosen()
        +kelolaDaftarPengajuan()
        +kelolaRubrikPenilaian()
        +kelolaPengaturanSistem()
        +cetakLaporan()
    }

    class ThesisSubmission {
        +String title
        +Text abstract
        +String research_field
        +String file_name
        +String file_type
        +String file_path
        +String status
        +Date submission_date
        +Date defense_date
        +Text notes
        +Decimal final_score
        +Boolean is_historical
        +canBeEditedByStudent() Boolean
        +getStatusLabel() String
        +getStatusBadgeClass() String
    }

    class Assessment {
        +Decimal total_score
        +Text comments
        +Text strengths
        +Text weaknesses
        +Text recommendations
        +Boolean is_submitted
        +Date submitted_at
    }

    class AssessmentScore {
        +Decimal score
        +Text notes
        +String criterion_name
        +Text criterion_description
        +Integer weight
    }

    class Rubric {
        +String name
        +Text description
        +Boolean is_active
    }

    class AssessmentCriterion {
        +String name
        +Text description
        +Integer max_score
        +Integer weight_percentage
        +Integer order
    }

    class Setting {
        +Integer maksimalBatchPengajuanSiklus
        +Integer maksimalPengajuanPerBatchSlotAktif
    }

    %% ==========================================
    %% HUBUNGAN PEWARISAN (INHERITANCE)
    %% ==========================================
    User <|-- Mahasiswa
    User <|-- Dosen
    User <|-- Kaprodi

    %% ==========================================
    %% HUBUNGAN ASOSIASI DAN MULTIPLISITAS (UMUM)
    %% ==========================================
    Mahasiswa "1" -- "*" ThesisSubmission : Mengajukan
    Dosen "1" -- "*" ThesisSubmission : Membimbing
    Dosen "1" -- "*" Assessment : Menilai
    Assessment "*" -- "1" ThesisSubmission : Dinilai
    ThesisSubmission "*" -- "0..1" Rubric : Menggunakan

    %% ==========================================
    %% HUBUNGAN ASOSIASI KAPRODI (MANAJEMEN & KONTROL)
    %% ==========================================
    Kaprodi "1" -- "*" Mahasiswa : Mengelola
    Kaprodi "1" -- "*" Dosen : Mengelola
    Kaprodi "1" -- "1" Setting : Mengatur
    Kaprodi "1" -- "*" ThesisSubmission : Mengelola & Mencetak Laporan
    Kaprodi "1" -- "*" Rubric : Mengelola

    %% ==========================================
    %% HUBUNGAN KOMPOSISI (COMPOSITION)
    %% ==========================================
    Assessment "1" *-- "*" AssessmentScore : Detail Nilai
    Rubric "1" *-- "*" AssessmentCriterion : Terdiri dari
```
