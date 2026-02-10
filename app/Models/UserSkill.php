<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    protected $fillable = [
        'user_id',
        'skill_id',
        'level',
        'years_experience',
    ];

    // =====================
    // Relationships
    // =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    // =====================
    // Level Helpers
    // =====================

    public function levelLabel(): string
    {
        return match ($this->level) {
            1 => 'Iniciante',
            2 => 'Básico',
            3 => 'Intermediário',
            4 => 'Avançado',
            5 => 'Expert',
            default => 'Não informado',
        };
    }
}

