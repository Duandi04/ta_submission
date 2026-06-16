<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.rbac.index', compact('roles', 'permissions'));
    }

    // Role CRUD
    public function createRole()
    {
        $permissions = Permission::all();
        return view('admin.rbac.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => strtolower($request->name),
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.rbac.index')->with('success', 'Role berhasil dibuat.');
    }

    public function editRole(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.rbac.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Prevent renaming core roles
        $coreRoles = ['admin', 'kaprodi', 'dosen', 'mahasiswa'];
        if (in_array($role->name, $coreRoles) && strtolower($request->name) !== $role->name) {
            return back()->with('error', 'Role bawaan sistem tidak boleh diubah namanya.');
        }

        $role->update([
            'name' => strtolower($request->name),
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.rbac.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroyRole(Role $role)
    {
        $coreRoles = ['admin', 'kaprodi', 'dosen', 'mahasiswa'];

        if (in_array($role->name, $coreRoles)) {
            return redirect()->route('admin.rbac.index')->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->route('admin.rbac.index')->with('success', 'Role berhasil dihapus.');
    }

    // Permission CRUD
    public function createPermission()
    {
        return view('admin.rbac.permissions.create');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
        ]);

        Permission::create([
            'name' => strtolower($request->name),
            'guard_name' => 'web'
        ]);

        return redirect()->route('admin.rbac.index')->with('success', 'Permission berhasil dibuat.');
    }

    public function editPermission(Permission $permission)
    {
        return view('admin.rbac.permissions.edit', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update([
            'name' => strtolower($request->name),
        ]);

        return redirect()->route('admin.rbac.index')->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.rbac.index')->with('success', 'Permission berhasil dihapus.');
    }
}
