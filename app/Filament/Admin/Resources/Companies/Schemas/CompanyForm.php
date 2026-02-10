<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Empresa')
                    ->schema([
                        Select::make('user_id')
                            ->label('Administrador')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('trade_name')
                            ->label('Nome Fantasia')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('legal_name')
                            ->label('Razão Social')
                            ->maxLength(255),

                        TextInput::make('document')
                            ->label('CNPJ')
                            ->mask('99.999.999/9999-99')
                            ->maxLength(20),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('companies/logos')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Contato')
                    ->schema([
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->prefix('https://'),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999'),
                    ])
                    ->columns(2),

                Section::make('Endereço')
                    ->schema([
                        TextInput::make('address')
                            ->label('Endereço')
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->label('Cidade'),

                        TextInput::make('state')
                            ->label('Estado')
                            ->maxLength(2),

                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->mask('99999-999'),
                    ])
                    ->columns(3),

                Section::make('Configurações')
                    ->schema([
                        Toggle::make('is_verified')
                            ->label('Empresa Verificada')
                            ->helperText('Empresas verificadas têm um selo de confiança'),
                    ]),
            ]);
    }
}

