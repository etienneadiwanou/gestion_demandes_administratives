<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return DepartmentResource::collection(Department::orderBy('nom')->paginate(20));
    }

    public function store(StoreDepartmentRequest $request)
    {
        $this->authorize('create', Department::class);

        $department = Department::create($request->validated());

        return DepartmentResource::make($department)->response()->setStatusCode(201);
    }

    public function show(Department $department)
    {
        return DepartmentResource::make($department);
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $this->authorize('update', $department);

        $department->update($request->validated());

        return DepartmentResource::make($department);
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);

        $department->delete();

        return response()->json(null, 204);
    }
}
