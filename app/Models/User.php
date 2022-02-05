<?php

namespace App\Models;

use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use App\Models\Interfaces\FilterableModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}
