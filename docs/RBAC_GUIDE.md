# Role-Based Access Control (RBAC) Guide

This document outlines the RBAC implementation in this project and provides guidelines for developers to maintain and extend it.

## Overview

The system uses the `spatie/laravel-permission` package to manage roles and permissions. Access control is enforced at multiple levels:
1.  **Routing**: Restricting access to entire groups of routes.
2.  **Views**: Showing/hiding UI elements based on user capabilities.
3.  **Controllers/Logic**: (Recommended) Using Laravel Policies for granular ownership and action checks.

## Key Principle: "Action vs View" Permissions

This system distinguishes between **Action Permissions** (what a user can do) and **View Permissions** (what a user can see in the UI).

1.  **Action Permissions** (e.g., `create submissions`, `approve submissions`):
    - Used in **Routes** (`routes/web.php`) and **Controllers**.
    - Prevents unauthorized access to backend logic.
2.  **View Permissions** (e.g., `view menu: reports`):
    - Used in **Blade Views** (especially `sidebar.blade.php`).
    - Controls the visibility of UI elements.

**DO NOT** check for roles directly in the code (e.g., `@role('admin')`).
**DO** check for the appropriate permission based on whether it's an action or a UI element.

---

## Roles and Permissions Mapping

| Role | Action Permissions | View/Menu Permissions |
| :--- | :--- | :--- |
| **Admin** | All permissions | All `view menu:` permissions |
| **Kaprodi** | `view users`, `approve submissions`, `assign lecturers`, `view reports`, `manage rubrics`, etc. | `view menu: kaprodi`, `view menu: dosen`, `view menu: reports` |
| **Dosen** | `view submissions`, `approve submissions`, `view assessments`, `create assessments` | `view menu: dosen` |
| **Mahasiswa**| `view submissions`, `create submissions`, `edit submissions` | `view menu: mahasiswa` |

---

## Implementation Guidelines

### 1. Adding a New Permission
1.  Add the permission name to `database/seeders/RolePermissionSeeder.php`.
2.  Assign it to the appropriate roles in the same seeder.
3.  Run `php artisan db:seed --class=RolePermissionSeeder` to update the database.

### 2. Protecting Routes
Use the `permission` middleware in `routes/web.php`:
```php
Route::middleware('permission:manage settings')->group(function () {
    // Routes here...
});
```

### 3. Conditional UI (Blade)
Use the `@can` directive:
```blade
@can('create submissions')
    <button>Submit Proposal</button>
@endcan
```

### 4. Controller Authorization
Use `$this->authorize()` or check on the user object:
```php
public function store(Request $request)
{
    $this->authorize('create submissions');
    // ...
}
```

---

## Maintenance

To refresh the RBAC system from the seeders:
```bash
php artisan migrate:fresh --seed
```
*Note: This will clear all data. To only update permissions without clearing data, you can run just the seeder.*
