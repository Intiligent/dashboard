<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\SettingDeletedEvent;
use Dashboard\Models\Setting;

class SettingDeletedListener
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
    public function handle(SettingDeletedEvent $event)
    {
        if ($event->setting->type === 'file') {
            if (File::exists(ltrim($event->setting->value, '/'))) {
                File::delete(ltrim($event->setting->value, '/'));
            }
        }
    }
}
