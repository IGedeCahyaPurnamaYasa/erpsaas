<?php

declare(strict_types=1);

namespace App\Models\Asset;

use App\Concerns\Blamable;
use App\Models\Accounting\Account;
use App\Models\Company;
use App\Observers\Asset\AssetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(AssetObserver::class)]
class Asset extends Model
{
    use HasFactory;
    use Blamable;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'asset_type',
        'serial_number',
        'tag_number',
        'status',
        'location',
        'assigned_to',
        'acquisition_date',
        'usage_date',
        'value',
        'depreciation_account_id',
        'expense_account_id',
        'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'usage_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function depreciationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_account_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    // public function maintenanceRecords(): HasMany
    // {
    //     return $this->hasMany(MaintenanceRecord::class);
    // }

    public function depreciations(): HasMany
    {
        return $this->hasMany(Depreciation::class);
    }
}