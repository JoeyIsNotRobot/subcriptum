<?php

namespace App\Filament\Admin\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Models\Category;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Básicas')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Select::make('company_id')
                            ->label('Empresa')
                            ->relationship('company', 'trade_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('categories')
                            ->label('Categorias')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        RichEditor::make('description')
                            ->label('Descrição')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('requirements')
                            ->label('Requisitos')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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

                        Select::make('status')
                            ->label('Status')
                            ->options(ProjectStatus::options())
                            ->default(ProjectStatus::Draft->value)
                            ->required(),

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
}

