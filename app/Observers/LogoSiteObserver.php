<?php

namespace App\Observers;

use App\Models\LogoSite;
use Illuminate\Support\Facades\Cache;

class LogoSiteObserver
{
    /**
     * Clear logo cache when logo is updated
     */
    public function saved(LogoSite $logoSite): void
    {
        Cache::forget('app_logo_site');
    }

    public function deleted(LogoSite $logoSite): void
    {
        Cache::forget('app_logo_site');
    }
}

