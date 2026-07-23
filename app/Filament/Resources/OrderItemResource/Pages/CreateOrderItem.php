<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;

    protected function getRedirectUrl(): string
    {
        $orderId = $this->record->order_id;

        return $this->getResource()::getUrl('index', ['order_id' => $orderId]);
    }
}
