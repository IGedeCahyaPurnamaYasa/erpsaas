<?php

declare(strict_types=1);

namespace App\Models\Asset;

use App\Concerns\Blamable;
use App\Models\Company;
use App\Observers\Asset\DepreciationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(DepreciationObserver::class)]
class Depreciation extends Model
{
    use HasFactory;
    use Blamable;

    protected $fillable = [
        'company_id',
        'asset_id',
        'date',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Depreciation $depreciation) {
            if (is_null($depreciation->company_id) && !is_null($depreciation->asset_id)) {
                $depreciation->company_id = $depreciation->asset->company_id;
            }
        });
        
        static::saving(function (Depreciation $depreciation) {
            if (is_null($depreciation->company_id) && !is_null($depreciation->asset_id)) {
                $depreciation->company_id = $depreciation->asset->company_id;
            }
        });
    }
}