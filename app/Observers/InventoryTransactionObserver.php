<?php

namespace App\Observers;

use App\Models\Inventory\InventoryTransaction;
use App\Models\Inventory\Inventory;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     */
    public function created(InventoryTransaction $transaction): void
    {
        $this->updateInventoryQuantity($transaction);
    }

    /**
     * Handle the InventoryTransaction "updated" event.
     */
    public function updated(InventoryTransaction $transaction): void
    {
        $this->recalculateInventoryQuantity($transaction);
    }

    /**
     * Handle the InventoryTransaction "deleted" event.
     */
    public function deleted(InventoryTransaction $transaction): void
    {
        $this->reverseInventoryQuantity($transaction);
    }

    /**
     * Update inventory quantity when transaction is created
     */
    protected function updateInventoryQuantity(InventoryTransaction $transaction): void
    {
        $inventory = $transaction->inventory;

        if ($inventory) {
            $quantityChange = $transaction->calculateQuantityChange();
            $inventory->quantity_on_hand += $quantityChange;
            $inventory->save();
        }
    }

    /**
     * Recalculate inventory quantity when transaction is updated
     */
    protected function recalculateInventoryQuantity(InventoryTransaction $transaction): void
    {
        $inventory = $transaction->inventory;

        if ($inventory) {
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

            $inventory->quantity_on_hand += $netChange;
            $inventory->save();
        }
    }

    /**
     * Reverse inventory quantity when transaction is deleted
     */
    protected function reverseInventoryQuantity(InventoryTransaction $transaction): void
    {
        $inventory = $transaction->inventory;

        if ($inventory) {
            $quantityChange = $transaction->calculateQuantityChange();
            // Reverse the change (opposite of what was applied)
            $inventory->quantity_on_hand -= $quantityChange;
            $inventory->save();
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