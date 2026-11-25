<?php

namespace App\Filament\Resources\CampaignSpendResource\Pages;

use App\Filament\Pages\StoreReports;
use App\Filament\Resources\CampaignSpendResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCampaignSpends extends ManageRecords
{
    protected static string $resource = CampaignSpendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('report')->label('أداء الحملات')->url(StoreReports::getUrl().'#campaigns')->outlined(),
            CreateAction::make()->label('إضافة إنفاق يومي')->createAnother(false),
        ];
    }
}
