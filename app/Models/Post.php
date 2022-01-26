<?php

namespace App\Models;

use App\Models\Interfaces\SortableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements SortableModelInterface
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'text',
        'user_id',
    ];

    /**
     * @return string
     */
    static function getDefaultSortField(): string
    {
        return 'id';
    }

    /**
     * @return string
     */
    static function getDefaultSortDirection(): string
    {
        return 'ASC';
    }

    /**
     * @return string[]
     */
    static function getAllowedSortFields(): array
    {
        return ['id', 'title', 'text', 'user_id', 'created_at', 'updated_at'];
    }

    static function getFiltrableColumns(): array
    {
        return ['id', 'title', 'text', 'user_id', 'created_at', 'updated_at'];
    }
}
