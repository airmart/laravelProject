<?php

namespace App\Models;

use App\Models\Interfaces\SortableModelInterface;
use App\Models\Interfaces\FilterableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements SortableModelInterface, FilterableModelInterface
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
    public static function getDefaultSortField(): string
    {
        return 'id';
    }

    /**
     * @return string
     */
    public static function getDefaultSortDirection(): string
    {
        return 'ASC';
    }

    /**
     * @return string[]
     */
    public static function getAllowedSortFields(): array
    {
        return ['id', 'title', 'text', 'user_id', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public static function getFilterableColumns(): array
    {
        return ['id', 'title', 'text', 'user_id', 'created_at', 'updated_at'];
    }
}
