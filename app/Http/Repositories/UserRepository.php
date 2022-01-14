<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository extends AbstractRepository
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return User::class;
    }
}
