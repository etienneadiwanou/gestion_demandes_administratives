<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Permission::class);

        return Permission::orderBy('module')->orderBy('nom')->get();
    }

    public function store(StorePermissionRequest $request)
    {
        $this->authorize('create', Permission::class);

        $permission = Permission::create($request->validated());

        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        return $permission;
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $permission->update($request->validated());

        return $permission;
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return response()->json(null, 204);
    }
}
