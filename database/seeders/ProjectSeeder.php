<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Category;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $categories = Category::all();

        if (!$company || $categories->isEmpty()) {
            return;
        }

        $projects = [
            [
                'title' => 'Desenvolvimento de Landing Page',
                'description' => '<p>Precisamos de uma landing page moderna e responsiva para nosso novo produto.</p><p>Requisitos:</p><ul><li>Design moderno e clean</li><li>Totalmente responsivo</li><li>Otimizado para SEO</li><li>Integração com Google Analytics</li></ul>',
                'requirements' => 'Experiência com HTML5, CSS3, JavaScript. Conhecimento em otimização de performance.',
                'budget_min' => 2000,
                'budget_max' => 5000,
                'max_candidates' => 10,
                'deadline' => now()->addDays(30),
                'estimated_duration_days' => 15,
                'is_remote' => true,
                'status' => ProjectStatus::Open,
                'published_at' => now(),
            ],
            [
                'title' => 'Criação de Identidade Visual',
                'description' => '<p>Buscamos um designer para criar a identidade visual completa da nossa marca.</p><p>Entregáveis:</p><ul><li>Logo em várias versões</li><li>Paleta de cores</li><li>Tipografia</li><li>Manual de marca</li></ul>',
                'requirements' => 'Portfólio com trabalhos anteriores de branding.',
                'budget_min' => 3000,
                'budget_max' => 8000,
                'max_candidates' => 5,
                'deadline' => now()->addDays(45),
                'estimated_duration_days' => 20,
                'is_remote' => true,
                'status' => ProjectStatus::Open,
                'published_at' => now(),
            ],
            [
                'title' => 'Aplicativo Mobile para Delivery',
                'description' => '<p>Desenvolvimento de aplicativo mobile (iOS e Android) para serviço de delivery.</p>',
                'requirements' => 'Experiência com React Native ou Flutter.',
                'budget_min' => 15000,
                'budget_max' => 30000,
                'max_candidates' => 8,
                'deadline' => now()->addDays(90),
                'estimated_duration_days' => 60,
                'is_remote' => true,
                'status' => ProjectStatus::Draft,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create([
                'company_id' => $company->id,
                'slug' => Str::slug($projectData['title']) . '-' . uniqid(),
                ...$projectData,
            ]);

            // Associar categorias aleatórias
            $project->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')
            );
        }
    }
}

