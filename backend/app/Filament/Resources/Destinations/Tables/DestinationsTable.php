<?php

namespace App\Filament\Resources\Destinations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DestinationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent.name')
                    ->label('Parent Area')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rank')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Governorate',
                        '2' => 'City',
                        '3' => 'Area',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'warning',
                        '3' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('price')
                    ->money('EGP')
                    ->label('Shipping Cost')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
