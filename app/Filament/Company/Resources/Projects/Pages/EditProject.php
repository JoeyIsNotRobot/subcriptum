<?php

namespace App\Filament\Company\Resources\Projects\Pages;

use App\Actions\Projects\PublishProjectAction;
use App\Actions\Projects\CancelProjectAction;
use App\Actions\Projects\CompleteProjectAction;
use App\Enums\ProjectStatus;
use App\Filament\Company\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publicar')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->visible(fn () => $this->record->isDraft())
                ->requiresConfirmation()
                ->modalHeading('Publicar Projeto')
                ->modalDescription('Tem certeza que deseja publicar este projeto? Ele ficará visível para todos os profissionais.')
                ->action(function () {
                    app(PublishProjectAction::class)->execute($this->record);

                    Notification::make()
                        ->title('Projeto publicado com sucesso!')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Action::make('complete')
                ->label('Marcar como Concluído')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->visible(fn () => $this->record->isInProgress())
                ->requiresConfirmation()
                ->modalHeading('Concluir Projeto')
                ->modalDescription('Tem certeza que deseja marcar este projeto como concluído?')
                ->action(function () {
                    app(CompleteProjectAction::class)->execute($this->record);

                    Notification::make()
                        ->title('Projeto concluído com sucesso!')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Action::make('cancel')
                ->label('Cancelar Projeto')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->canTransitionTo(ProjectStatus::Cancelled))
                ->requiresConfirmation()
                ->modalHeading('Cancelar Projeto')
                ->modalDescription('Tem certeza que deseja cancelar este projeto? Esta ação não pode ser desfeita.')
                ->action(function () {
                    app(CancelProjectAction::class)->execute($this->record);

                    Notification::make()
                        ->title('Projeto cancelado.')
                        ->warning()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}

