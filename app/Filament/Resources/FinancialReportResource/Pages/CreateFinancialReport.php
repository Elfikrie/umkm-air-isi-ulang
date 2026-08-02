<?php

namespace App\Filament\Resources\FinancialReportResource\Pages;

use App\Filament\Resources\FinancialReportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialReport extends CreateRecord
{
    protected static string $resource = FinancialReportResource::class;
    protected function getRedirectUrl():string{
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'manual';
        $data['created_by'] = auth()->id();

        return $data;
    }
}
