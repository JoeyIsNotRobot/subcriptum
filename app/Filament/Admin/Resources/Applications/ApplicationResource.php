<?php

namespace App\Filament\Admin\Resources\Applications;

use App\Enums\ApplicationStatus;
use App\Filament\Admin\Resources\Applications\Pages\ListApplications;
use App\Filament\Admin\Resources\Applications\Pages\ViewApplication;
use App\Models\Application;
use BackedEnum;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use UnitEnum;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Marketplace';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Candidatura';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Candidaturas';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Candidatura')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Profissional'),

                        TextEntry::make('project.title')
                            ->label('Projeto'),

                        TextEntry::make('project.company.trade_name')
                            ->label('Empresa'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => $state->color()),

                        TextEntry::make('proposed_value')
                            ->label('Valor Proposto')
                            ->money('BRL'),

                        TextEntry::make('estimated_days')
                            ->label('Prazo Estimado')
                            ->suffix(' dias'),

                        TextEntry::make('proposal_message')
                            ->label('Mensagem da Proposta')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Datas')
                    ->schema([
                        TextEntry::make('applied_at')
                            ->label('Candidatou-se em')
                            ->dateTime(),

                        TextEntry::make('reviewed_at')
                            ->label('Revisado em')
                            ->dateTime(),

                        TextEntry::make('accepted_at')
                            ->label('Aceito em')
                            ->dateTime(),

                        TextEntry::make('rejected_at')
                            ->label('Rejeitado em')
                            ->dateTime(),
                    ])
                    ->columns(4),

                Section::make('Mensagens do Chat')
                    ->schema([
                        TextEntry::make('messages_count')
                            ->label('Total de Mensagens')
                            ->state(fn ($record) => $record->messages()->count()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Profissional')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project.title')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('project.company.trade_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ApplicationStatus $state) => $state->color())
                    ->formatStateUsing(fn (ApplicationStatus $state) => $state->label())
                    ->sortable(),

                TextColumn::make('proposed_value')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('messages_count')
                    ->label('Mensagens')
                    ->counts('messages')
                    ->sortable(),

                TextColumn::make('applied_at')
                    ->label('Data')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ApplicationStatus::options()),

                SelectFilter::make('project')
                    ->label('Projeto')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('applied_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'view' => ViewApplication::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}



