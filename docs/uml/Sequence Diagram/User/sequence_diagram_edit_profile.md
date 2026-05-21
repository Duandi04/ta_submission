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
    UI-->>User: 2: Menampilkan Data Profil Saat Ini
    deactivate UI

    User->>UI: 3: Mengisi form edit profil & klik Simpan Perubahan
    activate UI
    UI->>Ctrl: 4: updateProfile(request)
    activate Ctrl
    Ctrl->>Ctrl: 5: Validate input data
    
    alt Sukses (Validasi Berhasil)
        Ctrl->>Model: 6: update(userData) & upload photo
        activate Model
        Model-->>Ctrl: 7: Profile Updated
        deactivate Model
        Ctrl-->>UI: 8: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>User: 9: Menampilkan Profil Baru & Alert Sukses
    else Gagal (Validasi Error)
        Ctrl-->>UI: 10: Redirect Back dengan Input & Errors
        deactivate Ctrl
        UI-->>User: 11: Menampilkan Pesan Error di Form Profil
    end
    deactivate UI
```
