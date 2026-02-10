<?php

namespace App\Filament\Company\Resources\Projects\Pages;

use App\Filament\Company\Resources\Projects\ProjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => !$this->record->isFinished()),
        ];
    }
}

