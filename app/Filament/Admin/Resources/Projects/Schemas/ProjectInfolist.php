<?php

namespace App\Filament\Admin\Resources\Projects\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Projeto')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título'),

                        TextEntry::make('company.trade_name')
                            ->label('Empresa'),

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

                        TextEntry::make('requirements')
                            ->label('Requisitos')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Orçamento e Prazo')
                    ->schema([
                        TextEntry::make('budget_min')
                            ->label('Orçamento Mínimo')
                            ->money('BRL'),

                        TextEntry::make('budget_max')
                            ->label('Orçamento Máximo')
                            ->money('BRL'),

                        TextEntry::make('deadline')
                            ->label('Prazo de Entrega')
                            ->date(),

                        TextEntry::make('estimated_duration_days')
                            ->label('Duração Estimada')
                            ->suffix(' dias'),
                    ])
                    ->columns(2),

                Section::make('Configurações')
                    ->schema([
                        TextEntry::make('max_candidates')
                            ->label('Máximo de Candidatos'),

                        TextEntry::make('applications_count')
                            ->label('Candidaturas')
                            ->state(fn ($record) => $record->applications()->count()),

                        IconEntry::make('is_remote')
                            ->label('Trabalho Remoto')
                            ->boolean(),

                        TextEntry::make('location')
                            ->label('Localização')
                            ->visible(fn ($record) => !$record->is_remote),
                    ])
                    ->columns(2),

                Section::make('Datas')
                    ->schema([
                        TextEntry::make('published_at')
                            ->label('Publicado em')
                            ->dateTime(),

                        TextEntry::make('started_at')
                            ->label('Iniciado em')
                            ->dateTime(),

                        TextEntry::make('completed_at')
                            ->label('Concluído em')
                            ->dateTime(),

                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}

