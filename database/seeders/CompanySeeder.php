<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companyAdmin = User::where('email', 'empresa@example.com')->first();

        if ($companyAdmin) {
            Company::create([
                'user_id' => $companyAdmin->id,
                'trade_name' => 'Tech Solutions',
                'legal_name' => 'Tech Solutions Ltda',
                'document' => '12.345.678/0001-90',
                'description' => 'Empresa de tecnologia focada em soluções digitais inovadoras.',
                'website' => 'https://techsolutions.com.br',
                'phone' => '(11) 99999-9999',
                'city' => 'São Paulo',
                'state' => 'SP',
                'is_verified' => true,
            ]);
        }
    }
}

