<?php

namespace App\Filament\Company\Resources\Stock\StockTransactionResource\Pages;

use App\Filament\Company\Resources\Stock\StockTransactionResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateStockTransaction extends CreateRecord
{
    protected static string $resource = StockTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['company_id'] = Auth::user()->current_company_id;

        return $data;
    }


}