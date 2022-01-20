<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository extends AbstractRepository
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Post::class;
    }
}
