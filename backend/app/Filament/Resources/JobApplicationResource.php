<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getNavigationLabel(): string
    {
        return 'Applications';
    }

    public static function getModelLabel(): string
    {
        return 'Application';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Job Applications';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Recruitment';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job')
                    ->schema([
                        Select::make('job_id')
                            ->relationship('job', 'title')
                            ->disabled(),
                    ]),

                Section::make('Personal Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('full_name')->disabled(),
                        \Filament\Forms\Components\TextInput::make('email')->disabled(),
                        \Filament\Forms\Components\TextInput::make('phone')->disabled(),
                        \Filament\Forms\Components\TextInput::make('date_of_birth')->disabled(),
                        \Filament\Forms\Components\TextInput::make('gender')->disabled(),
                        \Filament\Forms\Components\TextInput::make('address')->disabled(),
                        \Filament\Forms\Components\TextInput::make('city')->disabled(),
                    ])->columns(3),

                Section::make('Education & Training')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('education_level')->disabled(),
                        \Filament\Forms\Components\TextInput::make('university')->disabled(),
                        \Filament\Forms\Components\TextInput::make('graduation_year')->disabled(),
                        \Filament\Forms\Components\TextInput::make('major')->disabled(),
                        Textarea::make('certifications')->disabled()->columnSpanFull(),
                    ])->columns(2),

                Section::make('Experience')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('years_of_experience')->disabled(),
                        \Filament\Forms\Components\TextInput::make('current_job')->disabled(),
                        Textarea::make('previous_experience')->disabled()->columnSpanFull(),
                        Textarea::make('skills')->disabled()->columnSpanFull(),
                    ])->columns(2),

                Section::make('Additional')
                    ->schema([
                        Textarea::make('cover_letter')->disabled()->columnSpanFull(),
                    ]),

                Section::make('Admin Actions')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => '⏳ Pending',
                                'reviewed' => '👁 Reviewed',
                                'shortlisted' => '⭐ Shortlisted',
                                'accepted' => '✅ Accepted',
                                'rejected' => '❌ Rejected',
                            ])
                            ->required(),

                        Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('education_level')
                    ->label('Education')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('Exp. Years')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('job_id')
                    ->relationship('job', 'title')
                    ->label('Job'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListJobApplications::route('/'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
