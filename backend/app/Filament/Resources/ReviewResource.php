<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->disabled(),
                        
                        TextInput::make('rating')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(5),
                        
                        TextInput::make('title')
                            ->maxLength(255),
                        
                        Textarea::make('comment')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Section::make('Moderation')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        
                        Toggle::make('show_on_homepage')
                            ->label('Display on Homepage'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->sortable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Columns\IconColumn::make('show_on_homepage')
                    ->boolean()
                    ->label('Home'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('approve')
                    ->action(fn (Review $record) => $record->update(['status' => 'approved', 'approved_at' => now()]))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (Review $record) => $record->status === 'approved'),
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
            'index' => Pages\ListReviews::route('/'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
