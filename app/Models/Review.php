<?php

namespace App\Models;

use App\Enums\ReviewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'company_id',
        'reviewer_id',
        'reviewee_id',
        'type',
        'rating',
        'comment',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReviewType::class,
            'rating' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Projeto relacionado à avaliação.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Empresa relacionada à avaliação.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que fez a avaliação.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Usuário que recebeu a avaliação.
     */
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    // =====================
    // Helpers
    // =====================

    public function isFromCompany(): bool
    {
        return $this->type === ReviewType::CompanyToProfessional;
    }

    public function isFromProfessional(): bool
    {
        return $this->type === ReviewType::ProfessionalToCompany;
    }

    /**
     * Verifica se a avaliação é positiva (4-5 estrelas).
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Verifica se a avaliação é negativa (1-2 estrelas).
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }
}

