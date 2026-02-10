<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Desenvolvimento Web', 'icon' => 'heroicon-o-globe-alt'],
            ['name' => 'Design Gráfico', 'icon' => 'heroicon-o-paint-brush'],
            ['name' => 'Marketing Digital', 'icon' => 'heroicon-o-megaphone'],
            ['name' => 'Redação e Conteúdo', 'icon' => 'heroicon-o-document-text'],
            ['name' => 'Desenvolvimento Mobile', 'icon' => 'heroicon-o-device-phone-mobile'],
            ['name' => 'Vídeo e Animação', 'icon' => 'heroicon-o-video-camera'],
            ['name' => 'Consultoria', 'icon' => 'heroicon-o-light-bulb'],
            ['name' => 'Tradução', 'icon' => 'heroicon-o-language'],
            ['name' => 'Suporte Técnico', 'icon' => 'heroicon-o-wrench-screwdriver'],
            ['name' => 'Data Science', 'icon' => 'heroicon-o-chart-bar'],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }
    }
}

