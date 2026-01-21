<?php

namespace App\Filament\Company\Resources\Stock\StockTransactionResource\Pages;

use App\Filament\Company\Resources\Stock\StockTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ListStockTransactions extends ManageRecords
{
    protected static string $resource = StockTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}