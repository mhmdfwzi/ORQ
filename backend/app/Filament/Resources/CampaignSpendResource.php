<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignSpendResource\Pages\ManageCampaignSpends;
use App\Models\CampaignSpend;
use App\Support\CampaignAttribution;
use App\Support\ReportPeriod;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class CampaignSpendResource extends Resource
{
    protected static ?string $model = CampaignSpend::class;

    protected static ?string $modelLabel = 'إنفاق يومي';

    protected static ?string $pluralModelLabel = 'إنفاق الحملات';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): ?string
    {
        return 'التسويق';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('day')->label('يوم الإنفاق بتوقيت القاهرة')
                ->default(fn () => today(ReportPeriod::TIMEZONE)->toDateString())
                ->maxDate(fn () => today(ReportPeriod::TIMEZONE)->toDateString())->required(),
            TextInput::make('amount')->label('الإنفاق (ج.م)')
                ->numeric()->inputMode('decimal')->step(0.01)->minValue(0)->maxValue(9999999999.99)
                ->rules(['decimal:0,2'])->required(),
            ...collect(['utm_source' => 'المصدر', 'utm_medium' => 'الوسيط', 'utm_campaign' => 'اسم الحملة'])
                ->map(function (string $label, string $field): TextInput {
                    $input = TextInput::make($field)->label($label)->trim()->maxLength(100)->regex(CampaignAttribution::PATTERN)
                        ->mutateStateForValidationUsing(fn (?string $state) => $state === null ? null : CampaignAttribution::normalize($state))
                        ->dehydrateStateUsing(fn (string $state) => CampaignAttribution::normalize($state))->required();

                    if ($field === 'utm_campaign') {
                        $input->rule(fn (Get $get, ?CampaignSpend $record) => Rule::unique(CampaignSpend::class, 'utm_campaign')
                            ->where('day', $get('day'))
                            ->where('utm_source', CampaignAttribution::normalize((string) $get('utm_source')))
                            ->where('utm_medium', CampaignAttribution::normalize((string) $get('utm_medium')))
                            ->ignore($record?->getKey()))
                            ->validationMessages(['unique' => 'تم تسجيل إنفاق للحملة في اليوم ده. عدّل المبلغ من الجدول.']);
                    }

                    return $input;
                })->values()->all(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('day', 'desc')
            ->columns([
                TextColumn::make('day')->label('اليوم')->date('Y-m-d')->sortable(),
                TextColumn::make('utm_campaign')->label('الحملة')->searchable()->wrap(),
                TextColumn::make('utm_source')->label('المصدر')->searchable(),
                TextColumn::make('utm_medium')->label('الوسيط')->searchable(),
                TextColumn::make('amount')->label('الإنفاق')->money('EGP', locale: 'ar_EG')->sortable(),
            ])
            ->emptyStateHeading('مفيش إنفاق مسجّل')
            ->emptyStateDescription('أضف مبلغاً يومياً لكل حملة بنفس القيم الموجودة في رابط UTM.')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCampaignSpends::route('/')];
    }
}
