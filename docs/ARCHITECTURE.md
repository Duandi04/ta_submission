# Architecture & Design Patterns

This document explains the technical architecture and design patterns used in the TA Submission System.

## Core Principles

The system follows a **Service-Oriented Thin-Controller** approach to ensure high maintainability, testability, and scalability.

---

## 1. Directory Structure

### `app/Services`

Contains all business logic. Controllers should never perform database operations or complex calculations; they delegate to services.

- **Example**: `KaprodiService` handles proposal acceptance logic and NIM-based reporting.
- **Benefits**: Logic can be reused in Artisan commands, APIs, or other controllers.

### `app/Http/Requests`

Dedicated classes for input validation using Laravel's FormRequests.

- **Example**: `UserRequest` handles unique NIM/Email validation for both create and update operations.
- **Benefits**: Controllers remain clean and validation logic is centralized.

### `app/Models`

Eloquent models are kept "thin", containing only relationships, casts, and simple local scopes.

- **Scopes**: Used for common filtering (e.g., `scopeSearch`, `scopeByRole`).

---

## 2. Key Design Patterns

### Service Layer Pattern

Used to decouple the HTTP layer (Controllers) from the Business Logic layer.

- **Services** are stateless and typically injected into controllers.

### Form Request Pattern

Used for robust validation. This ensures that the controller only receives "safe" and validated data.

### Eloquent Scopes

Instead of writing complex `where` clauses in controllers, we encapsulate them in models.

```php
// In Controller
$students = User::role('mahasiswa')->filterByRequest($request)->paginate();
```

### Activity Logging

Integration with `spatie/laravel-activitylog` provides an automatic audit trail for critical models like `User`, `ThesisSubmission`, and `Assessment`.

---

## 3. Database Schema Highlights

- **Users**: Extended with `nim_nip`, `angkatan`, and `can_exceed_submission_limit`.
- **ThesisSubmissions**: Central table linking students, supervisors, and examiners.
- **Assessments**: Linked to **Rubrics** for flexible grading criteria.

---

## 4. Security

- **RBAC**: Managed via `spatie/laravel-permission`. Roles include `admin`, `kaprodi`, `koordinator`, `dosen`, and `mahasiswa`.
- **Authorization**: Handled via Middleware and Controller-level checks (e.g., ensuring a Kaprodi only manages students within their own Program Studi).
