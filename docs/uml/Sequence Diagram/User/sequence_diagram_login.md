# Sequence Diagram - Login

Diagram ini menunjukkan interaksi sistem saat pengguna (User) melakukan proses login ke dalam aplikasi pengajuan tugas akhir.

```mermaid
sequenceDiagram
    actor User as Lifeline1: User
    participant UI as UI Login Page
    participant Ctrl as AuthController
    participant Model as Model User

    User->>UI: 1: Memasukkan data login
    activate UI
    UI->>Ctrl: 2: GetUserController / login(request)
    activate Ctrl
    Ctrl->>Model: 3: ValidateData / attempt(credentials)
    activate Model
    Model-->>Ctrl: 4: ValidData / UserData
    deactivate Model
    
    alt Sukses
        Ctrl-->>UI: 5: Login Sukses (Session created)
        UI-->>User: 6: Menampilkan Halaman Dashboard
    else Gagal
        Ctrl-->>UI: 7: Login Gagal (Invalid credentials)
        deactivate Ctrl
        UI-->>User: 8: Menampilkan Pesan Error
    end
    deactivate UI
```
