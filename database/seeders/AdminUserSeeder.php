<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Create or update the panel administrator from the ADMIN_* settings.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->error('Define ADMIN_EMAIL y ADMIN_PASSWORD en el archivo .env antes de ejecutar este seeder.');

            return;
        }

        $admin = User::firstOrNew(['email' => $email]);
        $admin->forceFill([
            'name' => config('admin.name'),
            'password' => $password,
            'rol' => Rol::SuperAdmin,
            'email_verified_at' => now(),
        ])->save();
    }
}
