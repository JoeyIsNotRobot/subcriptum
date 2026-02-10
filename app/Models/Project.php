<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'description',
        'requirements',
        'budget_min',
        'budget_max',
        'max_candidates',
        'status',
        'deadline',
        'estimated_duration_days',
        'is_remote',
        'location',
        'published_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'deadline' => 'date',
            'is_remote' => 'boolean',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'published_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Empresa dona do projeto.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Categorias do projeto.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'project_categories');
    }

    /**
     * Candidaturas ao projeto.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Candidatura aceita (profissional selecionado).
     */
    public function acceptedApplication(): HasOne
    {
        return $this->hasOne(Application::class)->where('status', 'accepted');
    }

    /**
     * Profissional selecionado para o projeto.
     */
    public function selectedProfessional(): ?User
    {
        return $this->acceptedApplication?->user;
    }

    /**
     * Avaliações do projeto.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // =====================
    // Scopes
    // =====================

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::Open);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ProjectStatus::activeStatuses());
    }

    public function scopeByCategory(Builder $query, int|Category $category): Builder
    {
        $categoryId = $category instanceof Category ? $category->id : $category;

        return $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
    }

    // =====================
    // Status Helpers
    // =====================

    public function isDraft(): bool
    {
        return $this->status === ProjectStatus::Draft;
    }

    public function isOpen(): bool
    {
        return $this->status === ProjectStatus::Open;
    }

    public function isInProgress(): bool
    {
        return $this->status === ProjectStatus::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this->status === ProjectStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === ProjectStatus::Cancelled;
    }

    public function isFinished(): bool
    {
        return $this->status->isFinished();
    }

    public function allowsApplications(): bool
    {
        return $this->status->allowsApplications()
            && $this->applications()->count() < $this->max_candidates;
    }

    public function canTransitionTo(ProjectStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    // =====================
    // Application Helpers
    // =====================

    public function applicationsCount(): int
    {
        return $this->applications()->count();
    }

    public function hasAvailableSlots(): bool
    {
        return $this->applicationsCount() < $this->max_candidates;
    }

    public function hasUserApplied(User $user): bool
    {
        return $this->applications()->where('user_id', $user->id)->exists();
    }

    // =====================
    // Budget Helpers
    // =====================

    public function budgetRange(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return "R$ " . number_format($this->budget_min, 2, ',', '.')
                . " - R$ " . number_format($this->budget_max, 2, ',', '.');
        }

        if ($this->budget_min) {
            return "A partir de R$ " . number_format($this->budget_min, 2, ',', '.');
        }

        if ($this->budget_max) {
            return "Até R$ " . number_format($this->budget_max, 2, ',', '.');
        }

        return 'A combinar';
    }
}

