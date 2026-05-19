# Component Diagram

Component Diagram menggambarkan arsitektur modular perangkat lunak sistem pengajuan Tugas Akhir. Sistem ini menggunakan arsitektur **MVC (Model-View-Controller) dengan Service & Helper Layer** untuk memisahkan antarmuka pengguna, logika kontrol, logika bisnis, dan akses data secara bersih.

---

## 📊 Component Diagram (Mermaid Diagram)

```mermaid
graph TD
    %% Styling
    classDef default fill:#f9f9f9,stroke:#333,stroke-width:1px;
    classDef ui fill:#e3f2fd,stroke:#0d47a1,stroke-width:2px;
    classDef ctrl fill:#e8f5e9,stroke:#1b5e20,stroke-width:2px;
    classDef service fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef model fill:#f3e5f5,stroke:#4a148c,stroke-width:2px;
    classDef ext fill:#eceff1,stroke:#37474f,stroke-width:2px;

    subgraph User_Interface_Layer [Presentation Layer]
        UI[Blade Views & Assets <br> HTML / Bootstrap / CSS / JS]:::ui
    end

    subgraph Controller_Routing_Layer [Control Layer]
        Router[web.php <br> Laravel Router]:::ctrl
        
        AuthCtrl[AuthController <br> Auth & Sesi]:::ctrl
        ProfileCtrl[ProfileCtrl <br> Profil & Password]:::ctrl
        StudentCtrl[Student\SubmissionCtrl <br> Pengajuan Mhs]:::ctrl
        LecturerCtrl[Dosen\SubmissionCtrl <br> Bimbingan & Nilai]:::ctrl
        KaprodiCtrl[KaprodiCtrl <br> Kelola & Settings]:::ctrl
    end

    subgraph Business_Logic_Layer [Service Layer]
        AuthServ[AuthService]:::service
        SubServ[SubmissionService]:::service
        AssessServ[AssessmentService]:::service
        KaprodiServ[KaprodiService]:::service
        SuperServ[SupervisorService]:::service
    end

    subgraph Helper_Components [Helper & Core Utilities]
        SimHelper[SimilarityHelper <br> Cek Plagiarisme]:::ext
        NavHelper[NavigationHelper <br> Navigasi Detail]:::ext
    end

    subgraph Data_Access_Layer [Model & Storage Layer]
        UserModel[User Model]:::model
        ThesisModel[ThesisSubmission Model]:::model
        RubricModel[Rubric Model]:::model
        SettingModel[Setting Model]:::model
        AssessModel[Assessment Model]:::model
    end

    subgraph External_Integration_Packages [External Packages]
        ExcelLib[Maatwebsite Excel <br> Import & Export Engine]:::ext
        SpatieRole[Spatie Permissions <br> Role-Based Access Control]:::ext
        StorageSystem[Laravel Local Storage <br> File System]:::ext
    end

    %% Routing Connections
    UI -->|HTTP Request| Router
    Router --> AuthCtrl
    Router --> ProfileCtrl
    Router --> StudentCtrl
    Router --> LecturerCtrl
    Router --> KaprodiCtrl

    %% Controller to Service Connections
    AuthCtrl --> AuthServ
    StudentCtrl --> SubServ
    LecturerCtrl --> SuperServ
    LecturerCtrl --> AssessServ
    KaprodiCtrl --> KaprodiServ

    %% Service to Helper Connections
    SubServ -.-> NavHelper
    AssessServ -.-> SimHelper
    KaprodiServ -.-> NavHelper

    %% Service to Models Connections
    AuthServ --> UserModel
    SubServ --> ThesisModel
    AssessServ --> AssessModel
    KaprodiServ --> ThesisModel
    KaprodiServ --> RubricModel
    KaprodiServ --> SettingModel

    %% External Integrations Connections
    KaprodiServ --> ExcelLib
    UserModel -.-> SpatieRole
    SubServ --> StorageSystem
    
    %% Output to UI
    AuthCtrl -.->|Response HTML| UI
    StudentCtrl -.->|Response HTML| UI
    LecturerCtrl -.->|Response HTML| UI
    KaprodiCtrl -.->|Response HTML| UI
```

---

## 📝 Penjelasan Detail Komponen Sistem

1. **Presentation Layer (User Interface):**
   * **`Blade Views`**: Kumpulan berkas template `.blade.php` (frontend) yang menyajikan tampilan dashboard, form pengajuan, form penilaian, dan halaman laporan cetak kepada aktor (Mahasiswa, Dosen, Kaprodi) di browser.

2. **Control Layer (Routing & Controller):**
   * **`web.php`**: Menerima request HTTP dari browser dan mengarahkannya ke Controller yang sesuai berdasarkan pola URL.
   * **`Controllers`**: Berfungsi sebagai pengontrol alur aplikasi. Tugas utamanya adalah memvalidasi data masukan form (`Request`), memanggil logika bisnis di Service Layer, dan mengembalikan respon HTML/View yang sesuai.

3. **Service Layer (Logika Bisnis Terpusat):**
   * Lapisan terpenting yang menampung semua aturan bisnis (*business rules*). Contoh:
     * **`SubmissionService`**: Mengatur validasi batas deadline, penghitungan kuota pengajuan per batch (`max_batches` & `attempts_per_batch`), serta penyimpanan berkas fisik proposal.
     * **`AssessmentService`**: Mengatur proses perhitungan nilai rata-rata proposal dan detail sub-kriteria.

4. **Helper Components:**
   * **`SimilarityHelper`**: Berfungsi khusus untuk melakukan kalkulasi string `similar_text` guna mencocokkan judul proposal dengan arsip sejarah proposal.
   * **`NavigationHelper`**: Membantu memfasilitasi tombol navigasi *Previous/Next* saat Kaprodi/Dosen melihat detail data mahasiswa secara berurutan.

5. **Model & Storage Layer (Persistensi Data):**
   * **`Eloquent Models`**: Objek representasi tabel database yang mengurus pengambilan, penyuntingan, dan relasi data secara aman.
   * **`Storage System`**: Mengatur berkas proposal fisik yang disimpan di disk lokal server (`storage/app/private/` atau `storage/app/public/`).

6. **External Integration Packages:**
   * **`Maatwebsite Excel`**: Komponen mesin utama untuk membaca berkas Excel saat Kaprodi melakukan impor data mahasiswa/dosen, serta memformat lembar Excel saat melakukan ekspor data.
   * **`Spatie Permissions`**: Menangani otorisasi hak akses (*Role-Based Access Control*) guna memastikan Mahasiswa tidak dapat mengakses halaman Kaprodi, dan sebaliknya.
