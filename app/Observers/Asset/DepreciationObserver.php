<?php

declare(strict_types=1);

namespace App\Observers\Asset;

use App\Models\Asset\Depreciation;

class DepreciationObserver
{
    /**
     * Handle the Depreciation "created" event.
     */
    public function created(Depreciation $depreciation): void
    {
        // Log depreciation creation for audit trail
        // activity()
        //     ->performedOn($depreciation)
        //     ->causedBy($depreciation->created_by)
        //     ->log('Depreciation created: ' . $depreciation->asset->name . ' - ' . $depreciation->amount);
    }

    /**
     * Handle the Depreciation "updated" event.
     */
    public function updated(Depreciation $depreciation): void
    {
        // Log significant changes for audit trail
        if ($depreciation->wasChanged(['amount', 'date'])) {
            // activity()
            //     ->performedOn($depreciation)
            //     ->causedBy($depreciation->updated_by)
            //     ->log('Depreciation updated: ' . $depreciation->asset->name);
        }
    }

    /**
     * Handle the Depreciation "deleted" event.
     */
    public function deleted(Depreciation $depreciation): void
    {
        // Log depreciation deletion for audit trail
        // activity()
        //     ->performedOn($depreciation)
        //     ->causedBy($depreciation->updated_by)
        //     ->log('Depreciation deleted: ' . $depreciation->asset->name);
    }
}