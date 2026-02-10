<?php

namespace App\Filament\Company\Resources\Projects\Pages;

use App\Actions\Applications\AcceptApplicationAction;
use App\Actions\Applications\RejectApplicationAction;
use App\Enums\ApplicationStatus;
use App\Filament\Company\Resources\Projects\ProjectResource;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class ManageApplications extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.company.resources.projects.pages.manage-applications';

    public $record;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function resolveRecord($key)
    {
        return static::getResource()::getModel()::findOrFail($key);
    }

    public function getTitle(): string
    {
        return "Candidaturas - {$this->record->title}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl() => 'Projetos',
            static::getResource()::getUrl('view', ['record' => $this->record]) => $this->record->title,
            'Candidaturas',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Application::query()->where('project_id', $this->record->id))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Profissional')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ApplicationStatus $state) => $state->color())
                    ->formatStateUsing(fn (ApplicationStatus $state) => $state->label()),

                TextColumn::make('proposed_value')
                    ->label('Valor Proposto')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('estimated_days')
                    ->label('Prazo')
                    ->suffix(' dias'),

                TextColumn::make('applied_at')
                    ->label('Candidatura')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('messages_count')
                    ->label('Mensagens')
                    ->counts('messages'),
            ])
            ->recordActions([
                \Filament\Tables\Actions\Action::make('view_proposal')
                    ->label('Ver Proposta')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => "Proposta de {$record->user->name}")
                    ->modalContent(fn ($record) => view('filament.company.modals.application-proposal', [
                        'application' => $record,
                    ])),

                \Filament\Tables\Actions\Action::make('accept')
                    ->label('Aceitar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->canTransitionTo(ApplicationStatus::Accepted))
                    ->requiresConfirmation()
                    ->modalHeading('Aceitar Candidatura')
                    ->modalDescription(fn ($record) => "Ao aceitar {$record->user->name}, todas as outras candidaturas serão rejeitadas e o projeto iniciará.")
                    ->action(function ($record) {
                        app(AcceptApplicationAction::class)->execute($record);

                        Notification::make()
                            ->title('Candidatura aceita!')
                            ->body("O profissional {$record->user->name} foi selecionado.")
                            ->success()
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('reject')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->canTransitionTo(ApplicationStatus::Rejected))
                    ->requiresConfirmation()
                    ->modalHeading('Rejeitar Candidatura')
                    ->action(function ($record) {
                        app(RejectApplicationAction::class)->execute($record);

                        Notification::make()
                            ->title('Candidatura rejeitada.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('applied_at', 'desc');
    }
}

