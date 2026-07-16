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
    UI->>Ctrl: 2: changePasswordForm()
    activate Ctrl
    Ctrl-->>UI: 3: Mengembalikan View Form Ubah Password
    deactivate Ctrl
    UI-->>User: 4: Menampilkan Form Ubah Password
    deactivate UI

    User->>UI: 5: Memasukkan password lama, password baru & konfirmasi
    activate UI
    UI->>Ctrl: 6: updatePassword(request)
    activate Ctrl
    Ctrl->>Model: 7: Hash::check(password_lama)
    activate Model
    Model-->>Ctrl: 8: Verification Status
    deactivate Model

    alt Sukses (Password Lama Cocok & Validasi Berhasil)
        Ctrl->>Model: 9a: update(['password' => Hash::make(password_baru)])
        activate Model
        Model-->>Ctrl: 10a: Password Updated
        deactivate Model
        Ctrl-->>UI: 11a: Redirect Back dengan Pesan Sukses
        deactivate Ctrl
        UI-->>User: 12a: Menampilkan Alert Sukses Password Diperbarui
    else Gagal (Password Lama Salah / Konfirmasi Tidak Cocok / Validasi Error)
        Ctrl-->>UI: 9b: Redirect Back dengan Alert Error
        deactivate Ctrl
        UI-->>User: 10b: Menampilkan Form dengan Detail Error
    end
    deactivate UI
```
