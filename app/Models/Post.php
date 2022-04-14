<?php

namespace App\Models;

use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use App\Models\Interfaces\FilterableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property string $text
 * @property int $user_id
 * @property-read BelongsTo $user
 */
class Post extends Model implements SortableModelInterface, FilterableModelInterface, RelationableModelInterface
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
     * Get the user associated with the post.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

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

    /**
     * @return string[]
     */
    public static function getAvailableRelations(): array
    {
        return ['user'];
    }
}
