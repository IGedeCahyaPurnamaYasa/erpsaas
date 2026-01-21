<?php

namespace App\Filament\Company\Resources\Asset\AssetResource\Pages;

use App\Filament\Company\Resources\Asset\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetData extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}