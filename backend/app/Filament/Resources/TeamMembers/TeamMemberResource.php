<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Member Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('position')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('image')
                            ->image()
                            ->directory('team-images')
                            ->columnSpanFull(),
                        Textarea::make('bio')
                            ->rows(3)
                            ->columnSpanFull(),
                        KeyValue::make('social_links')
                            ->keyLabel('Platform (e.g. facebook, linkedin)')
                            ->valueLabel('URL')
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('position')->searchable(),
                ToggleColumn::make('is_active')->label('Active'),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
