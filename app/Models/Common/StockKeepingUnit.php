<?php

namespace App\Models\Common;

use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use Database\Factories\Common\StockKeepingUnitFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    use Blamable;
    use CompanyOwned;
    use HasFactory;

    protected $table = 'stock_keeping_units';

    protected $fillable = [
        'company_id',
        'code',
        'description',
        'increment',
        'created_by',
        'updated_by',
    ];

    protected static function newFactory(): Factory
    {
        return StockKeepingUnitFactory::new();
    }
}
