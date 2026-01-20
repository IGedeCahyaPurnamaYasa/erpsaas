<?php

namespace App\Filament\Company\Resources\Inventory\InventoryTransactionResource\Pages;

use App\Filament\Company\Resources\Inventory\InventoryTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryTransaction extends EditRecord
{
    protected static string $resource = InventoryTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


}