<?php

namespace App\Filament\Company\Resources\Asset\DepreciationResource\Pages;

use App\Filament\Company\Resources\Asset\DepreciationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepreciation extends CreateRecord
{
    protected static string $resource = DepreciationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $assetId = $this->getMountedActionArgument('asset');
        if ($assetId !== null) {
            $data['asset_id'] = $assetId;
        }
        
        return $data;
    }
}