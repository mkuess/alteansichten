<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the local development admin user.
     *
     * Credentials (development only):
     *   Email:    admin@alte-ansichten.local
     *   Password: Admin1234!
     *
     * Running this seeder multiple times is safe — it will not create duplicates.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@alte-ansichten.local'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'admin',
            ]
        );
    }
}
