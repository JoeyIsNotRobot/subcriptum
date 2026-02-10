<?php

declare(strict_types=1);

namespace App\Enums;

enum ReviewType: string
{
    case CompanyToProfessional = 'company_to_professional';
    case ProfessionalToCompany = 'professional_to_company';

    public function label(): string
    {
        return match ($this) {
            self::CompanyToProfessional => 'Avaliação do Profissional',
            self::ProfessionalToCompany => 'Avaliação da Empresa',
        };
    }

    public function reviewerLabel(): string
    {
        return match ($this) {
            self::CompanyToProfessional => 'Empresa',
            self::ProfessionalToCompany => 'Profissional',
        };
    }

    public function revieweeLabel(): string
    {
        return match ($this) {
            self::CompanyToProfessional => 'Profissional',
            self::ProfessionalToCompany => 'Empresa',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

