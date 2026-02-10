<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::UnderReview => 'Em Análise',
            self::Accepted => 'Aceita',
            self::Rejected => 'Rejeitada',
            self::Withdrawn => 'Retirada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::UnderReview => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
            self::Withdrawn => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::UnderReview => 'heroicon-o-eye',
            self::Accepted => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Withdrawn => 'heroicon-o-arrow-uturn-left',
        };
    }

    /**
     * Status que permite o chat.
     */
    public function allowsChat(): bool
    {
        return in_array($this, [
            self::Pending,
            self::UnderReview,
            self::Accepted,
        ], true);
    }

    /**
     * Status que indica decisão final.
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::Accepted,
            self::Rejected,
            self::Withdrawn,
        ], true);
    }

    /**
     * Transições válidas a partir do status atual.
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::UnderReview, self::Rejected, self::Withdrawn],
            self::UnderReview => [self::Accepted, self::Rejected],
            self::Accepted => [],
            self::Rejected => [],
            self::Withdrawn => [],
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions(), true);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

