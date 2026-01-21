<?php

namespace App\Filament\Company\Resources\Stock\StockResource\Pages;

use App\Filament\Company\Resources\Stock\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ListStockData extends ManageRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}