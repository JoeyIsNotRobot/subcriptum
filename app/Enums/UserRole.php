<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Professional = 'professional';
    case CompanyAdmin = 'company_admin';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Professional => 'Profissional',
            self::CompanyAdmin => 'Administrador de Empresa',
            self::Admin => 'Administrador',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Professional => 'info',
            self::CompanyAdmin => 'warning',
            self::Admin => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Professional => 'heroicon-o-user',
            self::CompanyAdmin => 'heroicon-o-building-office',
            self::Admin => 'heroicon-o-shield-check',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

