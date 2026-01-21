<?php

namespace App\Filament\Company\Resources\Asset\DepreciationResource\Pages;

use App\Filament\Company\Resources\Asset\DepreciationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ListDepreciations extends ManageRecords
{
    protected static string $resource = DepreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}