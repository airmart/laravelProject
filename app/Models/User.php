<?php

namespace App\Models;

use App\Constants\RoleConstants;
use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use App\Models\Interfaces\FilterableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property-read $posts
 * @property-read $role
 */
class User extends Authenticatable implements SortableModelInterface, FilterableModelInterface, RelationableModelInterface
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the post associated with the user.
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    /**
     * Get the role associated with the user.
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
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
        return ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public static function getFilterableColumns(): array
    {
        return ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public static function getAvailableRelations(): array
    {
        return ['posts'];
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role->role_name === RoleConstants::ADMIN_ROLE_NAME;
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role->role_name === RoleConstants::MODERATOR_ROLE_NAME;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role->role_name === RoleConstants::USER_ROLE_NAME;
    }
}
