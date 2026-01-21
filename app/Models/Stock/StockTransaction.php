<?php

namespace App\Models\Stock;

use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Models\Stock\Stock;
use App\Observers\StockTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(StockTransactionObserver::class)]
class StockTransaction extends Model
{
    use Blamable;
    use CompanyOwned;

    protected $fillable = [
        'company_id',
        'inventory_id',
        'transaction_type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'inventory_id');
    }

    public function scopeIn($query)
    {
        return $query->where('transaction_type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('transaction_type', 'out');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }

    public function isInTransaction(): bool
    {
        return $this->transaction_type === 'in';
    }

    public function isOutTransaction(): bool
    {
        return $this->transaction_type === 'out';
    }

    public function isAdjustment(): bool
    {
        return $this->transaction_type === 'adjustment';
    }

    /**
     * Calculate the quantity change for this transaction
     */
    public function calculateQuantityChange(): int
    {
        $quantity = $this->quantity;

        switch ($this->transaction_type) {
            case 'in':
                return $quantity;                 // Stock in: add positive quantity
            case 'out':
                return -$quantity;                // Stock out: subtract quantity
            case 'adjustment':
                return $quantity;                 // Adjustment: use quantity as-is (can be +/-)
            default:
                return 0;                         // Fallback: no change
        }
    }
}