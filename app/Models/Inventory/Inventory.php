<?php

namespace App\Models\Inventory;

use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Models\Common\Offering;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use Blamable;
    use CompanyOwned;

    protected $table = 'inventories';

    protected $fillable = [
        'company_id',
        'offering_id',
        'quantity_on_hand',
        'reorder_level',
        'max_stock_level',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'reorder_level' => 'integer',
        'max_stock_level' => 'integer',
    ];

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
    }

    public function needsReorder(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_level;
    }

    public function isOverstocked(): bool
    {
        return $this->quantity_on_hand > $this->max_stock_level;
    }

    public function isInStock(): bool
    {
        return $this->quantity_on_hand > 0;
    }
}