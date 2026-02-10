<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin do sistema
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        // Manter o usuário original
        User::create([
            'name' => 'admin',
            'email' => 'hectorcoelho@hotmail.com',
            'password' => bcrypt('123mudar'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        // Empresa de teste
        User::create([
            'name' => 'João Empresa',
            'email' => 'empresa@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::CompanyAdmin,
            'email_verified_at' => now(),
        ]);

        // Profissional de teste
        User::create([
            'name' => 'Maria Profissional',
            'email' => 'profissional@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Professional,
            'bio' => 'Desenvolvedora Full Stack com 5 anos de experiência em Laravel e Vue.js.',
            'email_verified_at' => now(),
        ]);

        // Mais profissionais
        User::factory()->count(10)->create([
            'role' => UserRole::Professional,
            'email_verified_at' => now(),
        ]);
    }
}
