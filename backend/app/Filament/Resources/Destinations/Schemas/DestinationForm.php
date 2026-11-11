<?php

namespace App\Filament\Resources\Destinations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DestinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Parent Area'),
                TextInput::make('name')
                    ->required()
                    ->label('Destination Name'),
                Select::make('rank')
                    ->options([
                        '1' => 'Governorate',
                        '2' => 'City',
                        '3' => 'Area'
                    ])
                    ->default('1')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label('Shipping Cost')
                    ->suffix('EGP'),
            ]);
    }
}
