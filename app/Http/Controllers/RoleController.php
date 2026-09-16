<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Role::with('permissions')->orderBy('nom')->get();
    }

    public function store(StoreRoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $role = Role::create($request->safe()->except('permissions'));

        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return $role->load('permissions');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $role->update($request->safe()->except('permissions'));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return $role->load('permissions');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->json(null, 204);
    }
}
