# Deployment Diagram

Deployment Diagram menggambarkan infrastruktur fisik tempat sistem pengajuan Tugas Akhir dijalankan dan disebarkan (*deployed*). Konfigurasi di bawah ini mencerminkan lingkungan server web standar yang digunakan untuk menjalankan aplikasi berbasis Laravel 11.

---

## 📊 Deployment Diagram (Mermaid Diagram)

```mermaid
graph TD
    %% Styling
    classDef client fill:#e3f2fd,stroke:#0d47a1,stroke-width:2px;
    classDef server fill:#e8f5e9,stroke:#1b5e20,stroke-width:2px;
    classDef database fill:#f3e5f5,stroke:#4a148c,stroke-width:2px;
    classDef storage fill:#fff3e0,stroke:#e65100,stroke-width:2px;

    subgraph Client_Tier [Client Device]
        Browser[User Browser <br> Chrome, Firefox, Edge, Safari]:::client
    end

    subgraph Application_Server_Node [Web & Application Server Node]
        WebServer[Web Server <br> Apache / Nginx]:::server
        
        subgraph Runtime_Environment [PHP Application Server]
            PHP[PHP 8.x Runtime <br> PHP-FPM / Mod PHP]:::server
            Laravel[Laravel 11 Core Framework <br> TA Submission App]:::server
        end
        
        subgraph File_System_Node [Local Storage Node]
            Disk[Local HDD / SSD Disk Storage <br> storage/app/public/proposal_files/]:::storage
        end
    end

    subgraph Database_Server_Node [Database Server Node]
        DB[Database Engine <br> MySQL / MariaDB Server]:::database
        Storage[Relational Tables <br> users, submissions, rubrics, settings]:::database
    end

    %% Protocols and Connections
    Browser -->|HTTP / HTTPS <br> Port: 80 / 443| WebServer
    WebServer -->|FastCGI / reverse-proxy| PHP
    PHP -->|Instantiates| Laravel
    
    Laravel -->|Local File System Read/Write| Disk
    Laravel -->|PDO MySQL Connection <br> TCP/IP Port: 3306| DB
    DB -->|SQL Queries & Transactions| Storage
```

---

## 📝 Penjelasan Detail Infrastruktur Fisik

1. **Client Device (Perangkat Pengguna):**
   * **`User Browser`**: Perangkat komputer, laptop, atau smartphone milik Mahasiswa, Dosen, dan Kaprodi. Mereka berinteraksi dengan sistem menggunakan peramban web modern (Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari) dengan mengirimkan permintaan HTTP/HTTPS.

2. **Web & Application Server Node (Server Web & Aplikasi):**
   * **`Web Server (Apache / Nginx)`**: Berfungsi sebagai gerbang depan penerima koneksi masuk dari jaringan publik/lokal pada **Port 80 (HTTP)** atau **Port 443 (HTTPS)**. Web server melayani aset-aset statis (gambar, CSS, Javascript) dan meneruskan request dinamis ke runtime PHP.
   * **`PHP 8.x Runtime`**: Lingkungan penerjemah bahasa PHP yang menjalankan inti framework Laravel.
   * **`Laravel 11 Core Framework`**: Aplikasi utama Tugas Akhir yang berisi semua file controller, service, helper, views, dan models.
   * **`Disk Storage (storage/app/public/)`**: Node penyimpanan lokal di dalam hard disk server yang digunakan khusus untuk menaruh berkas-berkas proposal PDF/Word yang diunggah oleh mahasiswa secara teratur.

3. **Database Server Node (Server Database Relasional):**
   * **`MySQL / MariaDB Server`**: Server terpisah (atau dalam mesin yang sama/localhost pada XAMPP) yang berjalan pada **Port 3306**.
   * **`Relational Database Storage`**: Menyimpan seluruh data relasional yang berstruktur rapi, seperti data akun pengguna, status persetujuan proposal, data nilai kriteria dari dosen penilai, serta pengaturan sistem Tugas Akhir per program studi. Koneksi dari server aplikasi ke server database dilakukan dengan aman menggunakan *PHP Data Objects* (PDO) MySQL.
