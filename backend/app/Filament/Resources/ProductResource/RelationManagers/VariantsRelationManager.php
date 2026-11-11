<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'المقاسات والأسعار';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('size')
                    ->label('المقاس')
                    ->options([
                        '50ml' => '50ml',
                        '100ml' => '100ml',
                    ])
                    ->required(),

                TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                TextInput::make('sale_price')
                    ->label('سعر التخفيض')
                    ->numeric()
                    ->prefix('$'),

                TextInput::make('stock_quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('size')
                    ->label('المقاس')
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('سعر التخفيض')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('الكمية'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
