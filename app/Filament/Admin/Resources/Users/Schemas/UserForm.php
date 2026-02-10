<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Básicas')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->label('Papel')
                            ->options(UserRole::options())
                            ->required()
                            ->default(UserRole::Professional->value),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999'),

                        DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em'),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->confirmed(),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar Senha')
                            ->password()
                            ->requiredWith('password')
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Perfil')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->circleCropper(),

                        Textarea::make('bio')
                            ->label('Biografia')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
