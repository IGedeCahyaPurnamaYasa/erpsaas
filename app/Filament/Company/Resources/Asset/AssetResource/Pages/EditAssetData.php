<?php

namespace App\Filament\Company\Resources\Asset\AssetResource\Pages;

use App\Filament\Company\Resources\Asset\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetData extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}