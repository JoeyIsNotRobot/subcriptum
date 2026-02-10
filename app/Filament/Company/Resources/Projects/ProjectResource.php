<?php

namespace App\Filament\Company\Resources\Projects;

use App\Actions\Projects\CreateProjectAction;
use App\Actions\Projects\PublishProjectAction;
use App\Enums\ProjectStatus;
use App\Filament\Company\Resources\Projects\Pages\CreateProject;
use App\Filament\Company\Resources\Projects\Pages\EditProject;
use App\Filament\Company\Resources\Projects\Pages\ListProjects;
use App\Filament\Company\Resources\Projects\Pages\ViewProject;
use App\Filament\Company\Resources\Projects\Pages\ManageApplications;
use App\Models\Project;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return 'Projeto';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Meus Projetos';
    }

    /**
     * Filtra projetos apenas da empresa do usuário logado.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasCompany()) {
            return $query->where('company_id', auth()->user()->company->id);
        }

        return $query->whereRaw('1 = 0'); // Retorna vazio se não tiver empresa
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Projeto')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('categories')
                            ->label('Categorias')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Descrição')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('requirements')
                            ->label('Requisitos')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Orçamento e Prazo')
                    ->schema([
                        TextInput::make('budget_min')
                            ->label('Orçamento Mínimo')
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01),

                        TextInput::make('budget_max')
                            ->label('Orçamento Máximo')
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01),

                        DatePicker::make('deadline')
                            ->label('Prazo de Entrega'),

                        TextInput::make('estimated_duration_days')
                            ->label('Duração Estimada (dias)')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2),

                Section::make('Configurações')
                    ->schema([
                        TextInput::make('max_candidates')
                            ->label('Máximo de Candidatos')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(100),

                        Toggle::make('is_remote')
                            ->label('Trabalho Remoto')
                            ->default(true),

                        TextInput::make('location')
                            ->label('Localização')
                            ->visible(fn ($get) => !$get('is_remote')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistSection::make('Informações do Projeto')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => $state->color()),

                        TextEntry::make('categories.name')
                            ->label('Categorias')
                            ->badge(),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                InfolistSection::make('Orçamento')
                    ->schema([
                        TextEntry::make('budget_min')
                            ->label('Mínimo')
                            ->money('BRL'),

                        TextEntry::make('budget_max')
                            ->label('Máximo')
                            ->money('BRL'),

                        TextEntry::make('deadline')
                            ->label('Prazo')
                            ->date(),

                        TextEntry::make('max_candidates')
                            ->label('Máx. Candidatos'),
                    ])
                    ->columns(4),

                InfolistSection::make('Candidaturas')
                    ->schema([
                        TextEntry::make('applications_count')
                            ->label('Total')
                            ->state(fn ($record) => $record->applications()->count()),

                        TextEntry::make('pending_applications')
                            ->label('Pendentes')
                            ->state(fn ($record) => $record->applications()->pending()->count()),

                        TextEntry::make('accepted_applications')
                            ->label('Aceitas')
                            ->state(fn ($record) => $record->applications()->accepted()->count()),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ProjectStatus $state) => $state->color())
                    ->formatStateUsing(fn (ProjectStatus $state) => $state->label())
                    ->sortable(),

                TextColumn::make('budget_max')
                    ->label('Orçamento')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('applications_count')
                    ->label('Candidaturas')
                    ->counts('applications')
                    ->sortable(),

                TextColumn::make('deadline')
                    ->label('Prazo')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_remote')
                    ->label('Remoto')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Criado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ProjectStatus::options()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => !$record->isFinished()),
                Action::make('applications')
                    ->label('Candidaturas')
                    ->icon('heroicon-o-users')
                    ->url(fn ($record) => static::getUrl('applications', ['record' => $record]))
                    ->visible(fn ($record) => $record->applications()->count() > 0),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
            'applications' => ManageApplications::route('/{record}/applications'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (!auth()->user()?->hasCompany()) {
            return null;
        }

        return (string) static::getEloquentQuery()
            ->where('status', ProjectStatus::Open)
            ->count();
    }
}


