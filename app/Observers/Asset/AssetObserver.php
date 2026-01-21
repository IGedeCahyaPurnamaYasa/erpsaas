<?php

declare(strict_types=1);

namespace App\Observers\Asset;

use App\Models\Asset\Asset;

class AssetObserver
{
    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        // Log asset creation for audit trail
        // activity()
        //     ->performedOn($asset)
        //     ->causedBy($asset->created_by)
        //     ->log('Asset created: ' . $asset->name);
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        // Log significant changes for audit trail
        if ($asset->wasChanged(['status', 'location', 'assigned_to', 'value'])) {
            // activity()
            //     ->performedOn($asset)
            //     ->causedBy($asset->updated_by)
            //     ->log('Asset updated: ' . $asset->name);
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        // Log asset deletion for audit trail
        // activity()
        //     ->performedOn($asset)
        //     ->causedBy($asset->updated_by)
        //     ->log('Asset deleted: ' . $asset->name);
    }
}