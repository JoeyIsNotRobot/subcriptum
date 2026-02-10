<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'status',
        'proposal_message',
        'proposed_value',
        'estimated_days',
        'applied_at',
        'reviewed_at',
        'accepted_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'proposed_value' => 'decimal:2',
            'applied_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Application $application) {
            $application->applied_at ??= now();
        });
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Profissional que se candidatou.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Projeto da candidatura.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Mensagens do chat desta candidatura.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    /**
     * Última mensagem do chat.
     */
    public function latestMessage(): ?ChatMessage
    {
        return $this->messages()->latest()->first();
    }

    // =====================
    // Scopes
    // =====================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::Pending);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::Accepted);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::Rejected);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ApplicationStatus::Pending,
            ApplicationStatus::UnderReview,
            ApplicationStatus::Accepted,
        ]);
    }

    // =====================
    // Status Helpers
    // =====================

    public function isPending(): bool
    {
        return $this->status === ApplicationStatus::Pending;
    }

    public function isUnderReview(): bool
    {
        return $this->status === ApplicationStatus::UnderReview;
    }

    public function isAccepted(): bool
    {
        return $this->status === ApplicationStatus::Accepted;
    }

    public function isRejected(): bool
    {
        return $this->status === ApplicationStatus::Rejected;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === ApplicationStatus::Withdrawn;
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function allowsChat(): bool
    {
        return $this->status->allowsChat() && $this->project->status->allowsChat();
    }

    public function canTransitionTo(ApplicationStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    // =====================
    // Chat Helpers
    // =====================

    public function unreadMessagesCount(User $user): int
    {
        return $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->count();
    }

    public function markMessagesAsRead(User $user): void
    {
        $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);
    }

    // =====================
    // Access Control
    // =====================

    /**
     * Verifica se o usuário pode acessar esta candidatura.
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Profissional dono da candidatura
        if ($this->user_id === $user->id) {
            return true;
        }

        // Admin da empresa dona do projeto
        if ($user->hasCompany() && $this->project->company_id === $user->company->id) {
            return true;
        }

        // Admin do sistema
        return $user->isAdmin();
    }
}

