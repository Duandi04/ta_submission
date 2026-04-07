# Class Diagram

Diagram ini menggambarkan entitas utama penyusun sistem beserta properti pokok dan strukturnya. Tidak mencakup fitur manajemen hak akses admin agar fokus pada proses pengajuan. Entitas Pengguna (User) secara hierarkis dibedakan berdasarkan perannya (Mahasiswa, Dosen, Kaprodi) untuk memperjelas batas fungsi masing-masing.

```mermaid
classDiagram
    class User {
        +BigInteger id
        +String name
        +String email
        +String nim_nip
        +Boolean is_active
        +Integer program_studi_id
        +login()
        +logout()
    }

    class Mahasiswa {
        +String roles = "mahasiswa"
        +isStudent() Boolean
    }

    class Dosen {
        +String roles = "dosen"
        +isLecturer() Boolean
        +isSupervisor() Boolean
    }

    class Kaprodi {
        +String roles = "kaprodi"
        +isCoordinator() Boolean
    }

    %% Inheritance to distinguish User roles
    User <|-- Mahasiswa
    User <|-- Dosen
    User <|-- Kaprodi

    class ThesisSubmission {
        +BigInteger id
        +BigInteger student_id
        +BigInteger supervisor_id
        +BigInteger supervisor_2_id
        +String title
        +Text abstract
        +String status
        +Date submission_date
        +Date defense_date
        +Decimal final_score
        +Text notes
        +getStatusLabel()
        +canBeEditedByStudent()
    }

    class SubmissionFile {
        +BigInteger id
        +BigInteger thesis_submission_id
        +String file_name
        +String file_type
        +String file_path
        +BigInteger uploaded_by
        +getFileTypeLabel()
        +getFormattedFileSize()
    }

    class Assessment {
        +BigInteger id
        +BigInteger thesis_submission_id
        +BigInteger evaluator_id
        +BigInteger rubric_id
        +Decimal total_score
        +Text comments
        +Boolean is_submitted
        +Date submitted_at
        +canBeEditedBy()
    }

    class Comment {
        +BigInteger id
        +BigInteger thesis_submission_id
        +BigInteger user_id
        +Text body
        +Timestamps created_at
    }

    class ProgramStudi {
        +BigInteger id
        +String name
        +BigInteger faculty_id
    }

    %% Relationships indicating clear actor assignments
    Mahasiswa "1" -- "*" ThesisSubmission : Mengajukan (Sebagai Mahasiswa)
    Dosen "1" -- "*" ThesisSubmission : Membimbing (Sebagai Pembimbing)
    Dosen "1" -- "*" Assessment : Menilai (Sebagai Penguji)
    
    User "1" -- "*" Comment : Menambahkan Komentar Diskusi
    
    ThesisSubmission "1" *-- "*" SubmissionFile : Terdiri dari
    ThesisSubmission "1" *-- "*" Assessment : Memiliki Nilai
    ThesisSubmission "1" *-- "*" Comment : Memiliki Diskusi Review
    
    ProgramStudi "1" -- "*" User : Menampung Anggota Program Studi
```
