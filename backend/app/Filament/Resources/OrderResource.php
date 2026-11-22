<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    /** Pending-orders counter shown next to "Orders" in the sidebar. */
    public static function getNavigationBadge(): ?string
    {
        $pending = Order::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sales';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        TextInput::make('order_number')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Select::make('user_id')
                            ->label('حساب العميل')
                            ->relationship('user', 'email', modifyQueryUsing: fn ($query) => $query->where('role', 'customer')->where('status', 'active'))
                            ->searchable(['name', 'email', 'phone'])
                            ->disabled(fn (Order $record): bool => $record->user_id !== null)
                            ->helperText('اربط طلب الضيف بعد تأكيد ملكية الطلب مع العميل.'),
                        
                        Select::make('status')
                            ->options(Order::STATUSES)
                            ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                            ->required()
                            ->native(false),
                        
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'refunded' => 'Refunded',
                            ])
                            ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                            ->required(),
                        
                        TextInput::make('payment_method')
                            ->disabled(),

                        Placeholder::make('paymob_reference')
                            ->label('Paymob transaction')
                            ->content(fn (Order $record): ?string => $record->paymob_transaction_id)
                            ->visible(fn (Order $record): bool => filled($record->paymob_transaction_id)),
                        
                        Placeholder::make('total')
                            ->content(fn (Order $record): string => 'EGP ' . number_format($record->total, 2)),

                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Section::make('Shipping Address')
                    ->schema([
                        Placeholder::make('address')
                            ->content(fn (Order $record): string => $record->shipping_address_text),
                        TextInput::make('shipping_carrier')->label('شركة الشحن')->maxLength(255),
                        TextInput::make('tracking_number')->label('رقم التتبع')->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->default(fn (Order $record) => $record->customer_display_name)
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options(Order::STATUSES)
                    ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                    ->updateStateUsing(function (Order $record, string $state): string {
                        $record->updateStatus($state);

                        return $state;
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->money('EGP')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'refunded' => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::STATUSES),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
