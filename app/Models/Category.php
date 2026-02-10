<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Projetos desta categoria.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_categories');
    }

    // =====================
    // Scopes
    // =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // =====================
    // Helpers
    // =====================

    public function projectsCount(): int
    {
        return $this->projects()->count();
    }

    public function openProjectsCount(): int
    {
        return $this->projects()->open()->count();
    }
}

