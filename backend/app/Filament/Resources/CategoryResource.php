<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    public static function getNavigationGroup(): ?string
    {
        return 'Catalog';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
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
                        
                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        
                        Textarea::make('description')
                            ->rows(3),
                        
                        FileUpload::make('image')
                            ->image()
                            ->directory('categories')
                            ->imageEditor(),
                        
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
                
                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('Leave empty to use category name'),
                        
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Recommended: 150-160 characters'),
                        
                        TagsInput::make('meta_keywords')
                            ->separator(','),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->relationship('parent', 'name')
                    ->label('Parent Category'),
                
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
