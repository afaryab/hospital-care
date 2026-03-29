<?php

namespace App\Observers;

use App\Models\Asset;

class AssetObserver
{
    public function creating(Asset $asset): void
    {
        if (empty($asset->asset_number)) {
            $asset->asset_number = Asset::generateAssetNumber();
        }
    }
}
