<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'sender_id',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Candidatura à qual a mensagem pertence.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Usuário que enviou a mensagem.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // =====================
    // Helpers
    // =====================

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Verifica se o usuário é o remetente.
     */
    public function isSentBy(User $user): bool
    {
        return $this->sender_id === $user->id;
    }

    /**
     * Verifica se o usuário pode visualizar a mensagem.
     */
    public function canBeViewedBy(User $user): bool
    {
        return $this->application->canBeAccessedBy($user);
    }
}

