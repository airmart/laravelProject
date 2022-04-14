<?php

namespace App\Models;

use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $text
 * @property int $user_id
 * @property int $post_id
 * @property-read $user
 * @property-read $post
 */
class Comment extends Model implements SortableModelInterface, RelationableModelInterface
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'text',
        'user_id',
        'post_id'
    ];

    /**
     * Get the user associated with the comment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the post associated with the comment.
     *
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(User::class, 'post_id', 'id');
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
    public static function getAvailableRelations(): array
    {
        return ['user', 'post'];
    }

    /**
     * @return string[]
     */
    public static function getAllowedSortFields(): array
    {
        return ['id', 'text', 'user_id', 'post_id', 'created_at', 'updated_at'];
    }
}
