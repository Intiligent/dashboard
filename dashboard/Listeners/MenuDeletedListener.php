<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\MenuDeletedEvent;
use App\Models\Menu;

class MenuDeletedListener
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
     * @param MenuDeletedEvent $event
     * @return void
     */
    public function handle(MenuDeletedEvent $event)
    {
        if ($event->group) {
            $value = Menu::getMenuByCode($event->group->code);
            Cache::put('navigation-menu-' . $event->group->code, $value);
        }
    }
}
