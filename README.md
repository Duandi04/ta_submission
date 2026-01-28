# Sistem Pengajuan Tugas Akhir (TA Submission System)

A web-based application for managing the Thesis (Tugas Akhir) submission process, built with **Laravel 12**. This system facilitates the interaction between students (mahasiswa), supervisors (dosen pembimbing), examiners (dosen penguji), coordinators (koordinator), and administrators.

## 🚀 Features

- **Role-Based Access Control**: Secure access management via `spatie/laravel-permission`.
- **Student Module**: Submit thesis proposals, upload drafts, track revision history, and view grades.
- **Supervisor & Examiner Module**: Manage guidance, provide revision notes, and input assessment rubrics.
- **Coordinator Module**: Oversee all submissions, manage program reports, and monitor progress.
- **Kaprodi Module**: Configure thesis settings (e.g., max drafts), manage assessment rubrics, and assign lecturers.
- **Admin Module**: Manage users, faculties, study programs, and system-wide configurations.
- **Activity Logging**: Full audit trail for every action using `spatie/laravel-activitylog`.
- **Clean Code Architecture**: Adheres to modern best practices (FormRequests, Service Layer, Thin Controllers).

## 🛠 Technology Stack

- **Backend Framework**: Laravel 12.x (PHP 8.2+)
- **Database**: MySQL / MariaDB (SQLite supported for testing)
- **Frontend**: Blade Templates, Bootstrap 5, SweetAlert2
- **Testing**: PHPUnit (Feature & Unit Tests)
- **Key Packages**:
  - `spatie/laravel-permission`
  - `spatie/laravel-activitylog`
  - `intervention/image`

## 🏗 Clean Code & Architecture

This project has been refactored to ensure high maintainability and scalability:

- **Service Layer**: Business logic is separated into dedicated Service classes (e.g., `AuthService`, `KaprodiService`).
- **Form Requests**: Input validation is moved from controllers to dedicated Request classes (e.g., `UserRequest`, `RubricRequest`).
- **Model Scopes**: encapsulated filtering and search logic within Eloquent Scopes for cleaner queries.
- **Thin Controllers**: Controllers only handle request routing and response returning.
- **Repository/Service Pattern**: logic for complex operations is decoupled from the Eloquent models.

## 📥 Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/Duandi04/ta_submission.git
   cd ta_submission
   ```

2. **Install PHP Dependencies**

   ```bash
   composer install
   ```

3. **Environment Setup**

   Copy `.env.example` to `.env` and configure your database.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration & Seeding**

   ```bash
   php artisan migrate --seed
   ```

5. **Run the Application**

   ```bash
   php artisan serve
   ```

## 🧪 Testing

The project includes automated tests to ensure stability.

```bash
# Run all tests
php artisan test

# Run specific feature tests
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/AdminUserTest.php
```

## 🔑 Default Credentials

Password for all accounts: `password`

| Role | Email |
| :--- | :--- |
| **Administrator** | `admin@ta.test` |
| **Koordinator** | `koordinator@ta.test` |
| **Kaprodi** | `kaprodi@ta.test` |
| **Dosen** | `dosen@ta.test` |
| **Mahasiswa** | `mahasiswa@ta.test` |

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
