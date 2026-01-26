# Sistem Pengajuan Tugas Akhir (TA Submission System)

A web-based application for managing the Thesis (Tugas Akhir) submission process, built with Laravel 11. This system facilitates the interaction between students (mahasiswa), supervisors (dosen pembimbing), examiners (dosen penguji), coordinators (koordinator), and administrators.

## Features

-   **Role-Based Access Control**: Secure access management for different user roles.
-   **Student Module**: Submit thesis proposals, track status, and view assessment results.
-   **Supervisor Module**: Review and manage supervised students' submissions.
-   **Examiner Module**: Input assessments and grades for assigned theses.
-   **Coordinator Module**: Oversee all submissions, assign examiners, and generate reports.
-   **Admin Module**: Manage users, roles, and view system activity logs.
-   **Activity Logging**: Comprehensive audit trail for system actions.
-   **Responsive Design**: Built with Bootstrap 5 and custom CSS for a modern user interface.

## Technology Stack

-   **Backend Framework**: Laravel 11 (PHP 8.2+)
-   **Database**: MySQL
-   **Frontend**: Blade Templates, Bootstrap 5 (CDN)
-   **Asset Management**: Custom CSS/JS (No Node.js/NPM required)

## Requirements

Before you begin, ensure you have the following installed on your machine:

-   PHP >= 8.2
-   Composer
-   MySQL

## Installation

1.  **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/ta_submission.git
    cd ta_submission
    ```

2.  **Install PHP Dependencies**

    ```bash
    composer install
    ```

3.  **Environment Setup**

    Copy the `.env.example` file to `.env` and configure your database settings.

    ```bash
    cp .env.example .env
    ```

    Update the database configuration in `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ta_submission
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

5.  **Run Migrations and Seeders**

    This will create the database tables and populate them with sample data (users, roles, permissions).

    ```bash
    php artisan migrate --seed
    ```

6.  **Run the Application**

    ```bash
    php artisan serve
    ```

    The application will be accessible at `http://localhost:8000`.

## Default Login Credentials

The application comes with the following default users (password for all is `password`):

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@ta.test` | `password` |
| **Koordinator** | `koordinator@ta.test` | `password` |
| **Dosen Pembimbing** | `pembimbing1@ta.test` | `password` |
| **Dosen Penguji** | `penguji1@ta.test` | `password` |
| **Mahasiswa** | `mahasiswa1@ta.test` | `password` |

## Project Structure

-   `app/Models`: Eloquent models (User, ThesisSubmission, Assessment, etc.)
-   `app/Http/Controllers`: Application logic and request handling.
-   `database/migrations`: Database schema definitions.
-   `database/seeders`: Initial data population.
-   `resources/views`: Blade templates for the frontend.
-   `routes/web.php`: Web application routes.

## Notes

-   This project does **not** rely on Node.js or NPM. All frontend assets rely on CDN links or static files in `public/`.
-   Queues are configured to run suitably for local development. If you use features requiring background jobs, run `php artisan queue:listen`.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
