<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Mecanico;
use Illuminate\Auth\Access\HandlesAuthorization;

class MecanicoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Mecanico');
    }

    public function view(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('View:Mecanico');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Mecanico');
    }

    public function update(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('Update:Mecanico');
    }

    public function delete(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('Delete:Mecanico');
    }

    public function restore(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('Restore:Mecanico');
    }

    public function forceDelete(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('ForceDelete:Mecanico');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Mecanico');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Mecanico');
    }

    public function replicate(AuthUser $authUser, Mecanico $mecanico): bool
    {
        return $authUser->can('Replicate:Mecanico');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Mecanico');
    }

}