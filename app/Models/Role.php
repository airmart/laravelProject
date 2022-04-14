<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $role_name
 * @property int $access_level
 */
class Role extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'role_name',
        'access_level'
    ];
}
