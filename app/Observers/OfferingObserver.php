<?php

namespace App\Observers;

use App\Models\Common\Offering;
use App\Models\Common\PriceHistory;
use App\Models\Common\StockKeepingUnit;

class OfferingObserver
{
    /**
     * Handle the Offering "created" event.
     */
    public function created(Offering $offering): void
    {
        //
    }

    public function saving(Offering $offering): void
    {
        $offering->clearSellableAdjustments();
        $offering->clearPurchasableAdjustments();

        if ($offering->sellable && $offering->stock_keeping_unit_id) {
            $sku_number = (int) $offering->stock_keeping_unit_number;

            StockKeepingUnit::find($offering->stock_keeping_unit_id)
            ->update([
                'increment' => $sku_number + 1
            ]);
        }
    }

    /**
     * Handle the Offering "updated" event.
     */
    public function updating(Offering $offering): void
    {
        if($offering->isDirty('stock_keeping_unit_id')){
            $sku_number = (int) $offering->stock_keeping_unit_number;
    
            StockKeepingUnit::find($offering->stock_keeping_unit_id)
            ->update([
                'increment' => $sku_number + 1
            ]);
        }
    }

    /**
     * Handle the Offering "updated" event.
     */
    public function updated(Offering $offering): void
    {
        if ($offering->isDirty(['price', 'unit'])) {
            $original = $offering->getOriginal();
            
            // Convert from cents to actual currency values for storage
            $beforePrice = $original['price'] / 100;
            $afterPrice = $offering->price / 100;
            
            $beforeUnitPrice = $offering->quantity > 0 ? round($beforePrice / $offering->quantity, 2) : $beforePrice;
            $afterUnitPrice = $offering->quantity > 0 ? round($afterPrice / $offering->quantity, 2) : $afterPrice;

            PriceHistory::create([
                'priceable_id' => $offering->id,
                'priceable_type' => Offering::class,
                'before' => $beforePrice,
                'after' => $afterPrice,
                'unit_price_before' => $beforeUnitPrice,
                'unit_price_after' => $afterUnitPrice,
            ]);
        }
    }

    /**
     * Handle the Offering "deleted" event.
     */
    public function deleted(Offering $offering): void
    {
        //
    }

    /**
     * Handle the Offering "restored" event.
     */
    public function restored(Offering $offering): void
    {
        //
    }

    /**
     * Handle the Offering "force deleted" event.
     */
    public function forceDeleted(Offering $offering): void
    {
        //
    }
}
