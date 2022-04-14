<?php

namespace Database\Seeders;

use App\Constants\RoleConstants;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'role_name' => RoleConstants::ADMIN_ROLE_NAME,
            'access_level' => RoleConstants::ADMIN_ACCESS_LEVEL
        ]);

        DB::table('roles')->insert([
            'role_name' => RoleConstants::MODERATOR_ROLE_NAME,
            'access_level' => RoleConstants::MODERATOR_ACCESS_LEVEL
        ]);

        DB::table('roles')->insert([
            'role_name' => RoleConstants::USER_ROLE_NAME,
            'access_level' => RoleConstants::USER_ACCESS_LEVEL
        ]);

        User::factory(10)->create();
        Post::factory(10)->create();
        Comment::factory(10)->create();
//        Role::factory(10)->create();
    }
}
