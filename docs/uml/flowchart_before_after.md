# Perbandingan Alur Kerja: Sebelum vs. Sesudah Sistem

Dokumen ini membandingkan alur kerja pengajuan proposal Tugas Akhir (TA) mahasiswa sebelum dan sesudah adanya sistem. Perbandingan ini menunjukkan transformasi dari **proses manual berbasis Google Drive & spreadsheet** menjadi **sistem terintegrasi** yang otomatis, aman, dan efisien.

---

## 📊 Flowchart Perbandingan (Mermaid Diagram)

```mermaid
flowchart LR
    %% ==========================================
    %% SEBELUM (PROSES MANUAL & GOOGLE DRIVE)
    %% ==========================================
    subgraph Sebelum["SEBELUM (Proses Manual & Google Drive)"]
        direction TB
        B_Start([● Mulai]) --> B1["Kaprodi buat folder utama di Google Drive"]
        B1 --> B2["Kaprodi bagikan link folder ke Mahasiswa"]
        B2 --> B3["Mahasiswa buat subfolder & unggah proposal TA"]
        B3 --> B4["Kaprodi buat Sheets rekap & susun rubrik nilai"]
        B4 --> B5["Kaprodi bagikan link Sheets rekap ke Dosen"]
        B5 --> B6["Dosen menilai proposal di Sheets masing-masing"]
        B6 --> B7["Kaprodi rekap nilai & tentukan Dosbing manual"]
        B7 --> B_End(((⦿ Selesai)))
    end

    %% ==========================================
    %% SESUDAH (SISTEM PENGAJUAN TA TERINTEGRASI)
    %% ==========================================
    subgraph Sesudah["SESUDAH (Sistem Pengajuan TA Terintegrasi)"]
        direction TB
        A_Start([● Mulai]) --> A1["Kaprodi atur deadline, batch & kuota di aplikasi"]
        A1 --> A2["Kaprodi susun rubrik & bobot nilai di sistem"]
        A2 --> A3["Kaprodi impor data Dosen & Mahasiswa via Excel"]
        A3 --> A4["Mahasiswa isi draf & kirim proposal online"]
        A4 --> A5{"Cek Orisinalitas<br/>& Judul"}
        
        %% Cabang Pemeriksaan Awal
        A5 -->|Tidak Layak| A6["Sistem memproses penolakan awal + alasan"]
        A5 -->|Layak| A7["Kaprodi validasi, tentukan Reviewer & rubrik"]
        
        A7 --> A8["Reviewer isi skor rubrik & ulasan kelayakan"]
        A8 --> A9{"Sidang Kelayakan<br/>& Nilai Akhir"}
        
        %% Cabang Evaluasi Akhir
        A9 -->|Tidak Lolos| A10["Sistem memproses penolakan akhir + feedback"]
        A9 -->|Lolos / Layak| A11["Sistem menyetujui & Kaprodi tentukan Dosbing 1 & 2"]
        
        A6 --> A_End(((⦿ Selesai)))
        A10 --> A_End
        A11 --> A_End
    end

    %% Styling
    classDef default fill:#fcfcfc,stroke:#333,stroke-width:1px;
    classDef sebelum fill:#ffebee,stroke:#c62828,stroke-width:1.5px;
    classDef sesudah fill:#e8f5e9,stroke:#2e7d32,stroke-width:1.5px;
    
    class B1,B2,B3,B4,B5,B6,B7 sebelum;
    class A1,A2,A3,A4,A5,A6,A7,A8,A9,A10,A11 sesudah;
```

---

## 📝 Tabel Komparasi Alur Kerja

| Aspek Kerja | Sebelum (Proses Manual) | Sesudah (Sistem Terintegrasi) | Keuntungan & Dampak Positif |
| :--- | :--- | :--- | :--- |
| **Inisiasi & Konfigurasi** | Kaprodi membuat folder Google Drive secara manual tiap siklus pengajuan. | Kaprodi mengatur deadline, batch, dan kuota percobaan pengajuan di aplikasi. | Menjamin batas waktu pengajuan secara otomatis dan disiplin. |
| **Pengelolaan Data Master** | Data dosen/mahasiswa diarsip di spreadsheet lokal, rawan terhapus/typo. | Data dosen/mahasiswa diimpor massal dari Excel ke database relasional. | Meningkatkan integritas data dan memangkas waktu input hingga 90%. |
| **Pengunggahan Proposal** | Mahasiswa mengunggah file ke Google Drive publik, rentan konflik akses/modifikasi. | Mahasiswa mengisi form online dan mengunggah file terenkripsi ke storage privat. | Keamanan file terjamin, mencegah manipulasi berkas pasca-pengajuan. |
| **Pemeriksaan & Plagiarisme** | Kaprodi membandingkan kemiripan judul baru secara manual dengan riwayat TA terdahulu. | Sistem mencocokkan orisinalitas judul secara real-time dengan database riwayat TA. | Mempercepat validasi judul dan mendeteksi duplikasi secara instan. |
| **Proses Penilaian** | Dosen menilai pada tab Google Sheets bersama, rentan diintip/diubah pihak lain. | Dosen mengisi rubrik interaktif terenkripsi langsung di dashboard pribadi. | Penilaian rahasia, kredibel, terstruktur, dan bebas risiko manipulasi. |
| **Penetapan Pembimbing** | Kaprodi menetapkan Dosbing secara manual dan mengumumkannya via WhatsApp/Email. | Sistem memperbarui status kelayakan dan memfasilitasi penetapan Dosbing 1 & 2 secara langsung. | Surat Keputusan instan dengan transparansi data bimbingan yang utuh. |

---

## 🚀 Kesimpulan Perubahan

Transformasi alur kerja ini memberikan tiga dampak utama bagi program studi:
1. **Keamanan Data**: Akses penilaian dosen dilindungi dengan sistem otorisasi (RBAC).
2. **Disiplin Waktu**: Pengajuan otomatis dikunci setelah batas waktu (deadline) berakhir.
3. **Transparansi**: Seluruh perubahan terekam (audit trail) untuk mencegah miskomunikasi.
