<?php

namespace App\Observers;

use App\Models\Stock\StockTransaction;
use App\Models\Stock\Stock;

class StockTransactionObserver
{
    /**
     * Handle the StockTransaction "created" event.
     */
    public function created(StockTransaction $transaction): void
    {
        $this->updateStockQuantity($transaction);
    }

    /**
     * Handle the StockTransaction "updated" event.
     */
    public function updated(StockTransaction $transaction): void
    {
        $this->recalculateStockQuantity($transaction);
    }

    /**
     * Handle the StockTransaction "deleted" event.
     */
    public function deleted(StockTransaction $transaction): void
    {
        $this->reverseStockQuantity($transaction);
    }

    /**
     * Update stock quantity when transaction is created
     */
    protected function updateStockQuantity(StockTransaction $transaction): void
    {
        $stock = $transaction->stock;

        if ($stock) {
            $quantityChange = $transaction->calculateQuantityChange();
            $stock->quantity_on_hand += $quantityChange;
            $stock->save();
        }
    }

    /**
     * Recalculate stock quantity when transaction is updated
     */
    protected function recalculateStockQuantity(StockTransaction $transaction): void
    {
        $stock = $transaction->stock;

        if ($stock) {
            // Get the original transaction data from the changes
            $original = $transaction->getOriginal();
            $current = $transaction->getAttributes();

            // Calculate original change
            $originalQuantity = $original['quantity'] ?? 0;
            $originalType = $original['transaction_type'] ?? 'in';
            $originalChange = $this->calculateChangeFromData($originalQuantity, $originalType);

            // Calculate current change
            $currentQuantity = $current['quantity'] ?? 0;
            $currentType = $current['transaction_type'] ?? 'in';
            $currentChange = $this->calculateChangeFromData($currentQuantity, $currentType);

            // Net change is current minus original
            $netChange = $currentChange - $originalChange;

            $stock->quantity_on_hand += $netChange;
            $stock->save();
        }
    }

    /**
     * Reverse stock quantity when transaction is deleted
     */
    protected function reverseStockQuantity(StockTransaction $transaction): void
    {
        $stock = $transaction->stock;

        if ($stock) {
            $quantityChange = $transaction->calculateQuantityChange();
            // Reverse the change (opposite of what was applied)
            $stock->quantity_on_hand -= $quantityChange;
            $stock->save();
        }
    }

    /**
     * Calculate quantity change from raw data
     */
    protected function calculateChangeFromData(int $quantity, string $transactionType): int
    {
        switch ($transactionType) {
            case 'in':
                return $quantity;
            case 'out':
                return -$quantity;
            case 'adjustment':
                return $quantity;
            default:
                return 0;
        }
    }
}