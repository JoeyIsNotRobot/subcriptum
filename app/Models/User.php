<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Empresa do usuário (se for company_admin).
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Candidaturas do profissional.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Mensagens enviadas pelo usuário.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Avaliações recebidas.
     */
    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Avaliações feitas.
     */
    public function givenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Skills do profissional.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    // =====================
    // Role Helpers
    // =====================

    public function isProfessional(): bool
    {
        return $this->role === UserRole::Professional;
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === UserRole::CompanyAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function hasCompany(): bool
    {
        return $this->isCompanyAdmin() && $this->company()->exists();
    }

    // =====================
    // Reputation
    // =====================

    /**
     * Média de avaliações recebidas.
     */
    public function averageRating(): float
    {
        return (float) $this->receivedReviews()->avg('rating') ?? 0.0;
    }

    /**
     * Total de avaliações recebidas.
     */
    public function totalReviews(): int
    {
        return $this->receivedReviews()->count();
    }

    /**
     * Projetos concluídos com sucesso.
     */
    public function completedProjects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Project::class,
            Application::class,
            'user_id',
            'id',
            'id',
            'project_id'
        )->where('projects.status', 'completed');
    }
}
