<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    public static function getNavigationLabel(): string
    {
        return 'Blog Posts';
    }

    public static function getModelLabel(): string
    {
        return 'Post';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Blog Posts';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Short summary for listings. Auto-generated from content if left empty.'),
                        
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog'),
                    ])->columns(2),
                
                Section::make('Arabic Content (Optional)')
                    ->schema([
                        TextInput::make('title_ar')
                            ->label('Title (Arabic)')
                            ->maxLength(255),
                        
                        Textarea::make('excerpt_ar')
                            ->label('Excerpt (Arabic)')
                            ->rows(3),
                        
                        RichEditor::make('content_ar')
                            ->label('Content (Arabic)')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog'),
                    ])->collapsed(),
                
                Section::make('Featured Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('blog')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675'),
                    ]),
                
                Section::make('Publishing')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->default(auth()->id())
                            ->required(),
                        
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->default(now()),
                        
                        Toggle::make('is_featured')
                            ->label('Featured Post'),
                    ])->columns(2),
                
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(60)
                            ->helperText('Max 60 characters'),
                        
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Max 160 characters'),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable()
                    ->label('Views'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
