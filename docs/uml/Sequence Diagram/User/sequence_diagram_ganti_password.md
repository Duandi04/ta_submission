# Sequence Diagram - Ganti Password

Diagram ini menunjukkan interaksi sistem saat pengguna (User) mengganti kata sandi/password mereka.

```mermaid
sequenceDiagram
    actor User as Lifeline1: User
    participant UI as UI Profile Page
    participant Ctrl as ProfileController
    participant Model as Model User

    User->>UI: 1: Membuka Halaman Ubah Password (pada Profil)
    activate UI
    UI-->>User: 2: Menampilkan Form Ubah Password
    deactivate UI

    User->>UI: 3: Memasukkan password lama, password baru & konfirmasi
    activate UI
    UI->>Ctrl: 4: updatePassword(request)
    activate Ctrl
    Ctrl->>Model: 5: Hash::check(password_lama)
    activate Model
    Model-->>Ctrl: 6: Verification Status
    deactivate Model

    alt Sukses (Password Lama Cocok & Validasi Berhasil)
        Ctrl->>Model: 7: update(['password' => Hash::make(password_baru)])
        activate Model
        Model-->>Ctrl: 8: Password Updated
        deactivate Model
        Ctrl-->>UI: 9: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>User: 10: Menampilkan Alert Sukses Password Diperbarui
    else Gagal (Password Lama Salah / Konfirmasi Tidak Cocok / Validasi Error)
        Ctrl-->>UI: 11: Redirect Back dengan Alert Error
        deactivate Ctrl
        UI-->>User: 12: Menampilkan Form dengan Detail Error
    end
    deactivate UI
```
