<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@ludexora.live')],
            [
                'name'     => env('ADMIN_NAME', 'Admin'),
                'password' => env('ADMIN_PASSWORD', '2021@Uni'),
                'is_admin' => true,
            ]
        );
    }
}
