<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cliente;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Secretario') || $user->hasRole('Administrador');
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Secretario') || $user->hasRole('Administrador');
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return $user->hasRole('Administrador');
    }
}
