<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\SettingDeleted;
use Dashboard\Models\Setting;

class SettingDeletedHook
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param SettingDeleted $event
     * @return void
     */
    public function handle(SettingDeleted $event)
    {
        $this->putStore();
    }

    /**
     * Store settings to cache
     *
     * @return void
     */
    public function putStore()
    {
        Cache::forever(Setting::CACHE_KEY, (new Setting)->getSettings()->toArray());
    }
}
