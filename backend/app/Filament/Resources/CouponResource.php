<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Str;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function getNavigationGroup(): ?string
    {
        return 'Sales';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Details')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->suffixAction(
                                \Filament\Actions\Action::make('generate')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(fn ($set) => $set('code', strtoupper(Str::random(8))))
                            ),
                        
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Discount Settings')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount ($)',
                            ])
                            ->required()
                            ->default('percentage'),
                        
                        TextInput::make('value')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        
                        TextInput::make('min_order_amount')
                            ->numeric()
                            ->label('Minimum Order Amount')
                            ->prefix('$'),
                        
                        TextInput::make('max_discount')
                            ->numeric()
                            ->label('Maximum Discount')
                            ->prefix('$')
                            ->helperText('For percentage coupons'),
                    ])->columns(2),

                Section::make('Usage Limits')
                    ->schema([
                        TextInput::make('usage_limit')
                            ->numeric()
                            ->label('Total Usage Limit')
                            ->helperText('Leave empty for unlimited'),
                        
                        TextInput::make('per_user_limit')
                            ->numeric()
                            ->default(1)
                            ->label('Per User Limit'),
                        
                        TextInput::make('usage_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->label('Times Used'),
                    ])->columns(3),

                Section::make('Validity Period')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Start Date'),
                        
                        DateTimePicker::make('expires_at')
                            ->label('Expiry Date'),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                
                TextColumn::make('formatted_discount')
                    ->label('Discount')
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('usage_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($record) => $record->usage_limit 
                        ? "{$record->usage_count}/{$record->usage_limit}" 
                        : $record->usage_count),
                
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->sortable(),
                
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
