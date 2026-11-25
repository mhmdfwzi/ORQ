<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'طلب';

    protected static ?string $pluralModelLabel = 'الطلبات';

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    public static function getNavigationBadge(): ?string
    {
        $pending = Order::where('status', 'pending')->where('payment_method', 'cod')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الطلبات';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الطلب')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('رقم الطلب')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Select::make('user_id')
                            ->label('حساب العميل')
                            ->relationship('user', 'email', modifyQueryUsing: fn ($query) => $query->where('role', 'customer')->where('status', 'active'))
                            ->searchable(['name', 'email', 'phone'])
                            ->disabled(fn (Order $record): bool => $record->user_id !== null)
                            ->helperText('اربط طلب الضيف بعد تأكيد ملكية الطلب مع العميل.'),
                        
                        Select::make('status')
                            ->label('حالة الطلب')
                            ->options(fn () => __('admin.order_status', locale: 'ar'))
                            ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                            ->required()
                            ->native(false),
                        
                        Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options(fn () => array_intersect_key(__('admin.payment_status', locale: 'ar'), array_flip(['pending', 'paid', 'refunded'])))
                            ->native(false)
                            ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                            ->required(),
                        
                        TextInput::make('payment_method')
                            ->label('طريقة الدفع')->disabled()
                            ->formatStateUsing(fn (?string $state): ?string => $state ? __('admin.payment_method.'.$state) : null),

                        Placeholder::make('paymob_reference')
                            ->label('مرجع عملية Paymob')
                            ->content(fn (Order $record): ?string => $record->paymob_transaction_id)
                            ->visible(fn (Order $record): bool => filled($record->paymob_transaction_id)),
                        
                        Placeholder::make('total')
                            ->label('الإجمالي')
                            ->content(fn (Order $record): string => number_format($record->total, 2).' ج.م'),

                        Textarea::make('notes')
                            ->label('الملاحظات')->columnSpanFull(),
                    ])->columns(2),
                
                Section::make('الشحن والتوصيل')
                    ->schema([
                        Placeholder::make('address')
                            ->label('عنوان التوصيل')->content(fn (Order $record): string => $record->shipping_address_text),
                        TextInput::make('shipping_carrier')->label('شركة الشحن')->maxLength(255),
                        TextInput::make('tracking_number')->label('رقم التتبع')->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل')
                    ->default(fn (Order $record) => $record->customer_display_name)
                    ->searchable(query: fn (Builder $query, string $search) => $query->where(
                        fn (Builder $matches) => $matches
                            ->whereLike('customer_name', '%'.$search.'%')
                            ->orWhereLike('customer_phone', '%'.$search.'%')
                            ->orWhereHas('user', fn (Builder $user) => $user
                                ->whereLike('name', '%'.$search.'%')
                                ->orWhereLike('phone', '%'.$search.'%')),
                    ))
                    ->sortable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->label('حالة الطلب')->native(false)->options(fn () => __('admin.order_status', locale: 'ar'))
                    ->disabled(fn (Order $record): bool => $record->paymob_order_id && $record->payment_status === 'pending')
                    ->updateStateUsing(function (Order $record, string $state): string {
                        if ($record->status !== $state) {
                            $record->updateStatus($state);
                        }

                        return $state;
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')->money('EGP', locale: 'ar_EG')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('حالة الدفع')->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.payment_status.'.$state))
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'refunded' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')->label('طريقة الدفع')
                    ->formatStateUsing(fn (string $state): string => __('admin.payment_method.'.$state))->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')->dateTime('d/m/Y H:i', timezone: 'Africa/Cairo')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الطلب')->options(fn () => __('admin.order_status', locale: 'ar'))->multiple(),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')->options(fn () => __('admin.payment_status', locale: 'ar')),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')->options(fn () => __('admin.payment_method', locale: 'ar')),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات العميل والطلب')->schema([
                TextEntry::make('customer_display_name')->label('العميل'),
                TextEntry::make('customer_display_phone')->label('رقم الهاتف')->copyable(),
                TextEntry::make('status')->label('حالة الطلب')->badge()->formatStateUsing(fn (string $state) => __('admin.order_status.'.$state)),
                TextEntry::make('payment_method')->label('طريقة الدفع')->formatStateUsing(fn (string $state) => __('admin.payment_method.'.$state)),
                TextEntry::make('payment_status')->label('حالة الدفع')->badge()->formatStateUsing(fn (string $state) => __('admin.payment_status.'.$state)),
                TextEntry::make('total')->label('الإجمالي')->money('EGP', locale: 'ar_EG'),
                TextEntry::make('created_at')->label('تاريخ الإنشاء')->dateTime('d/m/Y H:i', timezone: 'Africa/Cairo'),
                TextEntry::make('shipping_address_text')->label('عنوان التوصيل')->columnSpanFull(),
                TextEntry::make('shipping_carrier')->label('شركة الشحن')->placeholder('—'),
                TextEntry::make('tracking_number')->label('رقم التتبع')->placeholder('—'),
                TextEntry::make('paymob_transaction_id')->label('مرجع عملية Paymob')->visible(fn (Order $record) => filled($record->paymob_transaction_id)),
                TextEntry::make('notes')->label('ملاحظات العميل')->placeholder('—')->columnSpanFull(),
            ])->columns(3)->columnSpanFull(),
            Section::make('المنتجات المطلوبة')->schema([
                RepeatableEntry::make('items')->hiddenLabel()->schema([
                    TextEntry::make('product_name')->label('المنتج'),
                    TextEntry::make('size')->label('الحجم')->placeholder('—'),
                    TextEntry::make('quantity')->label('الكمية'),
                    TextEntry::make('unit_price')->label('سعر القطعة')->money('EGP', locale: 'ar_EG'),
                    TextEntry::make('total_price')->label('إجمالي البند')->money('EGP', locale: 'ar_EG'),
                ])->columns(5),
            ])->columnSpanFull(),
            Section::make('مصدر الحملة')->schema([
                TextEntry::make('utm_source')->label('المصدر'),
                TextEntry::make('utm_medium')->label('الوسيط'),
                TextEntry::make('utm_campaign')->label('الحملة'),
            ])->columns(3)->columnSpanFull()->visible(fn (Order $record) => filled($record->utm_source)),
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
