<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabled(fn (?Setting $record) => $record !== null)
                    ->live(),
                
                Toggle::make('value')
                    ->label('Enabled')
                    ->visible(fn ($get) => in_array($get('key'), ['registration_email_required', 'show_team_section']))
                    ->formatStateUsing(fn ($state) => (bool) $state)
                    ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                TextInput::make('value')
                    ->label('Value')
                    ->hidden(fn ($get) => in_array($get('key'), ['registration_email_required', 'show_team_section'])),
                
                TextInput::make('group')
                    ->required()
                    ->default('general'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('group', '!=', 'contact'))
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('value')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'contact' => 'Contact',
                        'social' => 'Social',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }
}
