<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\SettingGroupUpdated;
use Dashboard\Models\Setting;

class SettingGroupUpdatedHook
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
     * @param SettingGroupUpdated $event
     * @return void
     */
    public function handle(SettingGroupUpdated $event)
    {
        $keys = $event->group->settings->mapWithKeys(function($item) {
            return [$item['key'] => $item['value']];
        });
        if ($keys->get('LAYOUT_BOX_SIZE') !== null) {
            Cookie::queue('LAYOUT_BOX_SIZE', $keys->get('LAYOUT_BOX_SIZE'), 60 * 24 * 30);
        }
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
