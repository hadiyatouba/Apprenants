<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    private $rolePermissions = [
        'Admin' => ['Admin', 'Coach', 'Manager', 'CM', 'Apprenant'],
        'Manager' => ['Coach', 'Manager', 'CM', 'Apprenant'],
        'CM' => ['Apprenant'],
    ];

    public function store(User $user, string $newUserRole)
    {
        $currentUserRole = $user->role->name;
        return isset($this->rolePermissions[$currentUserRole]) && 
               in_array($newUserRole, $this->rolePermissions[$currentUserRole]);
    }

    public function update(User $currentUser, User $userToUpdate)
    {
        return $currentUser->id === $userToUpdate->id || $currentUser->role->name === 'Admin';
    }

    public function delete(User $currentUser, User $userToDelete)
    {
        return $currentUser->id === $userToDelete->id || $currentUser->role->name === 'Admin';
    }

    public function view(User $currentUser, User $userToView)
    {
        return $currentUser->id === $userToView->id || $currentUser->role->name === 'Admin';
    }

    public function createRole(User $user, string $roleToCreate)
    {
        $currentUserRole = $user->role->name;
        return in_array($roleToCreate, $this->rolePermissions[$currentUserRole] ?? []);
    }

    public function viewAny(User $user)
    {
        return $user->role->name === 'Admin';
    }

    public function manageAny(User $user)
    {
        return $user->role->name === 'Admin';
    }

    public function manageRole(User $user, string $role)
    {
        return $user->role->name === $role || $user->role->name === 'Admin';
    }
}