<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\MenuUpdated;
use App\Models\Menu;

class MenuUpdatedHook
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
     * @param MenuUpdated $event
     * @return void
     */
    public function handle(MenuUpdated $event)
    {
        // обновить кеш меню для текущей группы
        $group = $event->menu->ancestor();
        if ($group) {
            $value = Menu::getMenuByCode($group->code);
            Cache::put('navigation-menu-' . $group->code, $value);
            // Log::channel('dashboard')->info('update store menu [' . $group->code . ']');
        }
        // изменение группы меню. обновить группу куда перенесли
        if ($event->menu->wasChanged('parent_id')) {
            $parent = Menu::find($event->original->getOriginal('parent_id'));
            $items = Menu::getMenuByCode($parent->code);
            Cache::put('navigation-menu-' . $parent->code, $items);
            // Log::channel('dashboard')->info('update store menu [' . $parent->code . ']');
        }
    }
}
