<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->options([ProductVariant::STOREFRONT_SIZE => '50 مل'])
                    ->default(ProductVariant::STOREFRONT_SIZE)
                    ->required(),

                TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->required()
                    ->prefix('ج.م'),

                TextInput::make('sale_price')
                    ->label('سعر التخفيض')
                    ->numeric()
                    ->prefix('ج.م'),

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
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('size', ProductVariant::STOREFRONT_SIZE))
            ->columns([
                Tables\Columns\TextColumn::make('size')
                    ->label('المقاس')
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('سعر التخفيض')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('الكمية'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
