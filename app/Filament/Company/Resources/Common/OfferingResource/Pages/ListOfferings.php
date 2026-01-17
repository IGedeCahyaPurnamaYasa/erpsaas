<?php

namespace App\Filament\Company\Resources\Common\OfferingResource\Pages;

use App\Filament\Company\Resources\Common\OfferingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;

class ListOfferings extends ListRecords
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return 'max-w-8xl';
    }

    public function getTabs(): array
    {
        return [
            'sellable' => Tab::make()
                    ->label('Sellable')
                    ->modifyQueryUsing(function(Builder $query) {
                        $query->where('sellable', 1);
                    }),
            'purchasable' => Tab::make()
                    ->label('Purchasable')
                    ->modifyQueryUsing(function(Builder $query) {
                        $query->where('purchasable', 1);
                    }),
            'all'   => Tab::make()
                    ->label('All')
        ];
    }
}
