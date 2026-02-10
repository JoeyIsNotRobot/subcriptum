<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Empresa')
                    ->schema([
                        ImageEntry::make('logo')
                            ->label('Logo')
                            ->circular(),

                        TextEntry::make('trade_name')
                            ->label('Nome Fantasia'),

                        TextEntry::make('legal_name')
                            ->label('Razão Social'),

                        TextEntry::make('document')
                            ->label('CNPJ'),

                        TextEntry::make('user.name')
                            ->label('Administrador'),

                        IconEntry::make('is_verified')
                            ->label('Verificada')
                            ->boolean(),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Contato')
                    ->schema([
                        TextEntry::make('website')
                            ->label('Website')
                            ->url(),

                        TextEntry::make('phone')
                            ->label('Telefone'),
                    ])
                    ->columns(2),

                Section::make('Endereço')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Endereço'),

                        TextEntry::make('city')
                            ->label('Cidade'),

                        TextEntry::make('state')
                            ->label('Estado'),

                        TextEntry::make('zip_code')
                            ->label('CEP'),
                    ])
                    ->columns(4),

                Section::make('Estatísticas')
                    ->schema([
                        TextEntry::make('projects_count')
                            ->label('Total de Projetos')
                            ->state(fn ($record) => $record->projects()->count()),

                        TextEntry::make('completed_projects')
                            ->label('Projetos Concluídos')
                            ->state(fn ($record) => $record->completedProjects()),

                        TextEntry::make('average_rating')
                            ->label('Avaliação Média')
                            ->state(fn ($record) => number_format($record->averageRating(), 1))
                            ->suffix(' ⭐'),

                        TextEntry::make('created_at')
                            ->label('Cadastrada em')
                            ->dateTime(),
                    ])
                    ->columns(4),
            ]);
    }
}

