<?php

namespace App\Filament\Company\Resources\Stock\StockResource\Pages;

use App\Filament\Company\Resources\Stock\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockData extends CreateRecord
{
    protected static string $resource = StockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}