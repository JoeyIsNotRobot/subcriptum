<?php

namespace App\Filament\Company\Resources\Projects\Pages;

use App\Actions\Projects\CreateProjectAction;
use App\Filament\Company\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Associa automaticamente a empresa do usuário logado
        $data['company_id'] = auth()->user()->company->id;

        return $data;
    }
}

