# Sequence Diagram - Edit Profile

Diagram ini menunjukkan interaksi sistem saat pengguna (User) memperbarui informasi profil mereka.

```mermaid
sequenceDiagram
    actor User as Lifeline1: User
    participant UI as UI Profile Page
    participant Ctrl as ProfileController
    participant Model as Model User

    User->>UI: 1: Membuka Halaman Profil
    activate UI
    UI->>Ctrl: 2: edit()
    activate Ctrl
    Ctrl->>Model: 3: Auth::user()
    activate Model
    Model-->>Ctrl: 4: Data Profil Pengguna
    deactivate Model
    Ctrl-->>UI: 5: Mengembalikan View Profil
    deactivate Ctrl
    UI-->>User: 6: Menampilkan Data Profil Saat Ini
    deactivate UI

    User->>UI: 7: Mengisi form edit profil & klik Simpan Perubahan
    activate UI
    UI->>Ctrl: 8: updateProfile(request)
    activate Ctrl
    Ctrl->>Ctrl: 9: Validate input data
    
    alt Sukses (Validasi Berhasil)
        Ctrl->>Model: 10a: update(userData) & upload photo
        activate Model
        Model-->>Ctrl: 11a: Profile Updated
        deactivate Model
        Ctrl-->>UI: 12a: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>User: 13a: Menampilkan Profil Baru & Alert Sukses
    else Gagal (Validasi Error)
        Ctrl-->>UI: 10b: Redirect Back dengan Input & Errors
        deactivate Ctrl
        UI-->>User: 11b: Menampilkan Pesan Error di Form Profil
    end
    deactivate UI
```
