<?php

namespace App\Filament\Forms\Components;

use App\Models\Common\StockKeepingUnit;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;

class CreateStockKeepingUnitSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->searchable()
            ->live()
            ->createOptionForm($this->createAccountForm())
            ->createOptionAction(fn (Action $action) => $this->createAccountAction($action));

        $this->options(function () {
            $query = StockKeepingUnit::query();

            return $query->orderBy('code')
                ->pluck('code', 'id')
                ->toArray();
        });

        $this->createOptionUsing(static function (array $data) {
            return DB::transaction(static function () use ($data) {
                $account = StockKeepingUnit::create([
                    'code' => $data['code'],
                    'description' => $data['description'] ?? null,
                ]);

                return $account->getKey();
            });
        });
    }

    protected function createAccountForm(): array
    {
        return [
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->unique(table: StockKeepingUnit::class, column: 'code'),

            Textarea::make('description')
                ->label('Description'),
        ];
    }

    protected function createAccountAction(Action $action): Action
    {
        return $action
            ->label('Create SKU')
            ->slideOver()
            ->modalWidth(MaxWidth::Large)
            ->modalHeading('Create a new sku');
    }
}
