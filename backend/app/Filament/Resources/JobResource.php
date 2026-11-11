<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\JobListing;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Support\Str;

class JobResource extends Resource
{
    protected static ?string $model = JobListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    public static function getNavigationLabel(): string
    {
        return 'Jobs';
    }

    public static function getModelLabel(): string
    {
        return 'Job';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jobs';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Recruitment';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job Details')
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

                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),

                        RichEditor::make('requirements')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Arabic Content (Optional)')
                    ->schema([
                        TextInput::make('title_ar')
                            ->label('Title (Arabic)')
                            ->maxLength(255),

                        RichEditor::make('description_ar')
                            ->label('Description (Arabic)')
                            ->columnSpanFull(),

                        RichEditor::make('requirements_ar')
                            ->label('Requirements (Arabic)')
                            ->columnSpanFull(),
                    ])->collapsed(),

                Section::make('Job Info')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'full-time' => 'Full Time',
                                'part-time' => 'Part Time',
                                'remote' => 'Remote',
                                'contract' => 'Contract',
                            ])
                            ->default('full-time')
                            ->required(),

                        TextInput::make('department')
                            ->maxLength(255),

                        TextInput::make('location')
                            ->maxLength(255),

                        DatePicker::make('deadline')
                            ->label('Application Deadline'),

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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full-time' => 'success',
                        'part-time' => 'warning',
                        'remote' => 'info',
                        'contract' => 'primary',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('department')
                    ->searchable(),

                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applicants')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'full-time' => 'Full Time',
                        'part-time' => 'Part Time',
                        'remote' => 'Remote',
                        'contract' => 'Contract',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
