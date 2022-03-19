<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends AbstractRepository
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Comment::class;
    }
}
