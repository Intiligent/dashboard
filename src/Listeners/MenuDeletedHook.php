<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\MenuDeleted;
use App\Models\Menu;

class MenuDeletedHook
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
     * @param MenuDeleted $event
     * @return void
     */
    public function handle(MenuDeleted $event)
    {
        if ($event->group) {
            $value = Menu::getMenuByCode($event->group->code);
            Cache::put('navigation-menu-' . $event->group->code, $value);
        }
    }
}
