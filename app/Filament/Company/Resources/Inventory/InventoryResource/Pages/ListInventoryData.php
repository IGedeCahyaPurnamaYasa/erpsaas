<?php

namespace App\Filament\Company\Resources\Inventory\InventoryResource\Pages;

use App\Filament\Company\Resources\Inventory\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ListInventoryData extends ManageRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}