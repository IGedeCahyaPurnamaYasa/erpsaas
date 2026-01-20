<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PriceHistory extends Model
{
    protected $fillable = [
        'priceable_id',
        'priceable_type',
        'before',
        'after',
        'unit_price_before',
        'unit_price_after',
    ];

    protected $casts = [
        'before' => 'integer',
        'after' => 'integer',
        'unit_price_before' => 'decimal:2',
        'unit_price_after' => 'decimal:2',
    ];

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }
}