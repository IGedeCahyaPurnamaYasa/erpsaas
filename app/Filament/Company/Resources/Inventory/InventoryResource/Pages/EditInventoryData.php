<?php

namespace App\Filament\Company\Resources\Inventory\InventoryResource\Pages;

use App\Filament\Company\Resources\Inventory\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryData extends EditRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}