<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Jalankan: php artisan db:seed --class=UserSeeder
     * atau tambahkan di DatabaseSeeder.php lalu: php artisan db:seed
     */
    public function run(): void
    {
        // Buat akun Admin
        User::updateOrCreate(
            ['email' => 'admin@bandarkode.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@bandarkode.com',
                'password' => Hash::make('Malang0341'),
                'role'     => User::ROLE_ADMIN,
            ]
        );

        // Buat akun Client
        User::updateOrCreate(
            ['email' => 'client@bandarkode.com'],
            [
                'name'     => 'Client User',
                'email'    => 'client@bandarkode.com',
                'password' => Hash::make('Malang0341'),
                'role'     => User::ROLE_CLIENT,
            ]
        );

        $this->command->info('✅ Users berhasil dibuat!');
        $this->command->table(
            ['Name', 'Email', 'Role'],
            [
                ['Super Admin', 'admin@bandarkode.com',  'Admin (1)'],
                ['Client User', 'client@bandarkode.com', 'Client (2)'],
            ]
        );
    }
}
