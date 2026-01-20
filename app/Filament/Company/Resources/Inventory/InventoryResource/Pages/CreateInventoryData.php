<?php

namespace App\Filament\Company\Resources\Inventory\InventoryResource\Pages;

use App\Filament\Company\Resources\Inventory\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryData extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}