<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\MenuUpdatedEvent;
use App\Models\Menu;

class MenuUpdatedListener
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
     * @param MenuUpdatedEvent $event
     * @return void
     */
    public function handle(MenuUpdatedEvent $event)
    {
        // update cache for current group
        $group = $event->menu->ancestor();
        if ($group) {
            $value = Menu::getMenuByCode($group->code);
            Cache::put('navigation-menu-' . $group->code, $value);
        }
        // change menu group. update destination group
        if ($event->menu->wasChanged('parent_id')) {
            $parent = Menu::find($event->original->getOriginal('parent_id'));
            $items = Menu::getMenuByCode($parent->code);
            Cache::put('navigation-menu-' . $parent->code, $items);
        }
    }
}
