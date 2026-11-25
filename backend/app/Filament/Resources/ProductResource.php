<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function getNavigationGroup(): ?string
    {
        return 'الكتالوج';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Product Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => 
                                        $set('slug', Str::slug($state))),
                                
                                TextInput::make('name_ar')
                                    ->label('Arabic Name')
                                    ->maxLength(255),
                                
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                
                                Textarea::make('short_description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                
                                Textarea::make('short_description_ar')
                                    ->label('Short Description (Arabic)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                
                                RichEditor::make('description')
                                    ->columnSpanFull(),
                                
                                RichEditor::make('description_ar')
                                    ->label('Description (Arabic)')
                                    ->columnSpanFull(),
                            ])->columns(2),
                        
                        Section::make('Gallery')
                            ->schema([
                                FileUpload::make('images')
                                    ->label('صور المنتج')
                                    ->helperText('اسحب الصور لتغيير ترتيبها. الصورة الأولى هي اللي بتظهر في كارت المنتج.')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('products')
                                    ->image()
                                    ->imageEditor()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->visibility('public')
                                    ->openable()
                                    ->downloadable()
                                    // Bundled storefront images are outside the upload disk.
                                    ->fetchFileInformation(false)
                                    ->getUploadedFileUsing(static function (FileUpload $component, string $file): ?array {
                                        $disk = $component->getDisk();
                                        $uploaded = ! str_starts_with($file, '/') && ! preg_match('#^https?://#i', $file);

                                        if ($uploaded && ! $disk->exists($file)) {
                                            return null;
                                        }

                                        return [
                                            'name' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                                            'size' => $uploaded ? $disk->size($file) : 0,
                                            'type' => $uploaded ? $disk->mimeType($file) : 'application/octet-stream',
                                            'url' => static::dashboardImageUrl($file),
                                        ];
                                    }),
                            ]),
                        
                        Section::make('تفاصيل العطر')
                            ->schema([
                                TextInput::make('subtitle')
                                    ->label('الشعار الفرعي')
                                    ->maxLength(255),
                                TextInput::make('brand_feel')
                                    ->label('إحساس العلامة')
                                    ->maxLength(255),
                                TextInput::make('scent_type')
                                    ->label('نوع الرائحة')
                                    ->maxLength(255),
                                Select::make('mood')
                                    ->label('الطابع')
                                    ->native(false)->placeholder('اختر الطابع')
                                    ->options(__('admin.mood', locale: 'ar')),
                                Toggle::make('is_best_seller')
                                    ->label('الأكثر مبيعاً'),
                                Toggle::make('is_new')
                                    ->label('جديد'),
                                Toggle::make('is_bundle')
                                    ->label('صندوق هدايا'),
                            ])->columns(2),

                        Section::make('نوتات العطر')
                            ->schema([
                                TagsInput::make('notes_top')
                                    ->label('النوتات العليا (Top Notes)')
                                    ->placeholder('أضف نوتة'),
                                TagsInput::make('notes_heart')
                                    ->label('نوتات القلب (Heart Notes)')
                                    ->placeholder('أضف نوتة'),
                                TagsInput::make('notes_base')
                                    ->label('النوتات القاعدية (Base Notes)')
                                    ->placeholder('أضف نوتة'),
                            ])->columns(3),

                        Section::make('SEO Settings')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->maxLength(255),
                                
                                Textarea::make('meta_description')
                                    ->rows(2)
                                    ->maxLength(160),
                                
                                TagsInput::make('meta_keywords')
                                    ->separator(','),
                            ])->collapsible(),
                    ])->columnSpan(2),
                
                Group::make()
                    ->schema([
                        Section::make('Pricing & Inventory')
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'MED-' . strtoupper(Str::random(8)))
                                    ->dehydrateStateUsing(fn ($state) => $state ?: 'MED-' . strtoupper(Str::random(8))),
                                
                                TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$'),
                                
                                TextInput::make('sale_price')
                                    ->numeric()
                                    ->prefix('$'),
                                
                                TextInput::make('stock_quantity')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                
                                TextInput::make('manufacturer'),
                                
                                TextInput::make('size')
                                    ->placeholder('e.g. 100ml, 50g'),
                            ]),
                        
                        Section::make('Offers & Discounts')
                            ->icon('heroicon-o-fire')
                            ->description('Configure special offers for this product')
                            ->schema([
                                TextInput::make('offer_label')
                                    ->label('Offer Label')
                                    ->placeholder('e.g. Summer Sale, Flash Deal')
                                    ->maxLength(100),
                                
                                DateTimePicker::make('offer_start_date')
                                    ->label('Start Date')
                                    ->native(false),
                                
                                DateTimePicker::make('offer_end_date')
                                    ->label('End Date')
                                    ->native(false)
                                    ->after('offer_start_date'),
                                
                                Toggle::make('is_offer_featured')
                                    ->label('Featured in Homepage Offers')
                                    ->helperText('Show this offer in the homepage offers section'),
                            ])
                            ->collapsible(),
                        
                        Section::make('Status')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ])
                                    ->default('published')
                                    ->required(),
                                
                                Toggle::make('is_featured')
                                    ->label('Featured Product'),
                                
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Image')
                    ->disk('public')
                    ->visibility('public')
                    ->state(function (Product $record): ?string {
                        $path = $record->galleryPaths()[0] ?? null;

                        return $path === null ? null : static::dashboardImageUrl($path);
                    })
                    ->circular(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => $record->sku ?? ''),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($state) => $state < 10 ? 'danger' : 'success'),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),

                Tables\Columns\TextColumn::make('mood')
                    ->label('الطابع')
                    ->formatStateUsing(fn (string $state): string => __('admin.mood.'.$state, locale: 'ar'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'royal_oud' => 'warning',
                        'night_allure' => 'danger',
                        'sporty_fresh' => 'success',
                        'warm_formal' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_best_seller')
                    ->label('بست سيلر')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),

                Tables\Filters\SelectFilter::make('mood')
                    ->label('الطابع')->options(__('admin.mood', locale: 'ar')),

                Tables\Filters\Filter::make('low_stock')
                    ->label('مخزون منخفض')
                    ->query(fn ($query) => $query->published()->whereHas('variants', fn ($variants) => $variants->forSale()->where('stock_quantity', '<=', 5))),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            VariantsRelationManager::class,
        ];
    }

    private static function dashboardImageUrl(string $path): string
    {
        if (str_starts_with($path, '/media/')) {
            return rtrim(config('app.frontend_url'), '/') . $path;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return str_starts_with($path, '/') ? url($path) : Storage::disk('public')->url($path);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
