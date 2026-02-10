<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'trade_name',
        'legal_name',
        'document',
        'document_type',
        'description',
        'logo',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Usuário administrador da empresa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Projetos da empresa.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Avaliações recebidas pela empresa.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // =====================
    // Helpers
    // =====================

    /**
     * Média de avaliações.
     */
    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating') ?? 0.0;
    }

    /**
     * Total de projetos publicados.
     */
    public function totalProjects(): int
    {
        return $this->projects()->count();
    }

    /**
     * Projetos concluídos com sucesso.
     */
    public function completedProjects(): int
    {
        return $this->projects()->where('status', 'completed')->count();
    }
}

