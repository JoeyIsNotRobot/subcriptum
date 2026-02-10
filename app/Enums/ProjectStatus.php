<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Selecting = 'selecting';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Open => 'Aberto',
            self::Selecting => 'Em Seleção',
            self::InProgress => 'Em Andamento',
            self::Completed => 'Concluído',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Open => 'success',
            self::Selecting => 'warning',
            self::InProgress => 'info',
            self::Completed => 'primary',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Open => 'heroicon-o-globe-alt',
            self::Selecting => 'heroicon-o-user-group',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    /**
     * Status que permite candidaturas.
     */
    public function allowsApplications(): bool
    {
        return $this === self::Open;
    }

    /**
     * Status que permite chat ativo.
     */
    public function allowsChat(): bool
    {
        return in_array($this, [
            self::Open,
            self::Selecting,
            self::InProgress,
        ], true);
    }

    /**
     * Status que indica projeto finalizado.
     */
    public function isFinished(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Cancelled,
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
            self::Draft => [self::Open, self::Cancelled],
            self::Open => [self::Selecting, self::Cancelled],
            self::Selecting => [self::InProgress, self::Open, self::Cancelled],
            self::InProgress => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
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

    public static function activeStatuses(): array
    {
        return [
            self::Open,
            self::Selecting,
            self::InProgress,
        ];
    }
}

