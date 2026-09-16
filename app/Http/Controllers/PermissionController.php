<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Permission::class);

        return PermissionResource::collection(Permission::orderBy('module')->orderBy('nom')->get());
    }

    public function store(StorePermissionRequest $request)
    {
        $this->authorize('create', Permission::class);

        $permission = Permission::create($request->validated());

        return PermissionResource::make($permission)->response()->setStatusCode(201);
    }

    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        return PermissionResource::make($permission);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $permission->update($request->validated());

        return PermissionResource::make($permission);
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return response()->json(null, 204);
    }
}
