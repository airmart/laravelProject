<?php

namespace App\Models;

use App\Models\Interfaces\SortableModelInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements SortableModelInterface
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
        return ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'];
    }

    static function getFiltrableColumns(): array
    {
        return ['id', 'name', 'email', 'email_verified_at'];
    }
}
