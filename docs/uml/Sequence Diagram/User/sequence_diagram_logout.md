# Sequence Diagram - Logout

Diagram ini menunjukkan interaksi sistem saat pengguna (User) melakukan proses logout dari aplikasi.

```mermaid
sequenceDiagram
    actor User as Lifeline1: User
    participant UI as UI Dashboard Page
    participant Ctrl as AuthController

    User->>UI: 1: Mengklik tombol Logout
    activate UI
    UI->>Ctrl: 2: logout()
    activate Ctrl
    Ctrl->>Ctrl: 3: Invalidate session & regenerate token
    Ctrl-->>UI: 4: Logout Sukses
    deactivate Ctrl
    UI-->>User: 5: Mengalihkan ke Halaman Login
    deactivate UI
```
