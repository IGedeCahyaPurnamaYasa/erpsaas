<?php

namespace App\Filament\Company\Resources\Stock\StockResource\Pages;

use App\Filament\Company\Resources\Stock\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockData extends EditRecord
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}