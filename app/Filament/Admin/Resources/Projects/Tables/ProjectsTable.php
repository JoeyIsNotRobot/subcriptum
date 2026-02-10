<?php

namespace App\Filament\Admin\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('company.trade_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                SelectFilter::make('company')
                    ->label('Empresa')
                    ->relationship('company', 'trade_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('is_remote')
                    ->label('Tipo de Trabalho')
                    ->options([
                        '1' => 'Remoto',
                        '0' => 'Presencial',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

