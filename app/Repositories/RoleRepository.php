<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends AbstractRepository
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Role::class;
    }
}
