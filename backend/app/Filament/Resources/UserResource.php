<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationGroup(): ?string
    {
        return 'Users';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(6),
                    ])->columns(2),
                
                Section::make('Permissions & Status')
                    ->schema([
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'customer' => 'Customer',
                            ])
                            ->required(),
                        
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'blocked' => 'Blocked',
                            ])
                            ->required(),
                        
                        DateTimePicker::make('phone_verified_at')
                            ->label('Phone Verified At'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'customer' => 'success',
                        default => 'secondary',
                    }),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'active' => 'Active',
                        'blocked' => 'Blocked',
                    ]),
                
                Tables\Columns\IconColumn::make('phone_verified_at')
                    ->boolean()
                    ->label('Verified')
                    ->getStateUsing(fn (User $record): bool => (bool) $record->phone_verified_at),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'blocked' => 'Blocked',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
