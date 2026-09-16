<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return User::with(['role', 'department'])
            ->orderBy('name')
            ->paginate(20);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        return response()->json($user->load('role', 'department'), 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return $user->load('role', 'department');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user->load('role', 'department');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Désactivation plutôt que suppression : on conserve la
        // traçabilité des actions passées de l'utilisateur.
        $user->update(['actif' => false]);

        return response()->json(null, 204);
    }
}
