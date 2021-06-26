<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\CategoryUpdated;
use App\Models\Category;

class CategoryUpdatedHook
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
     * @param CategoryUpdated $event
     * @return void
     */
    public function handle(CategoryUpdated $event)
    {
        // обновить кеш каталога
        // $group = Menu::ancestorsOf($event->menu->id)->first()->group;
        // if ($group) {
        //     $value = Menu::getMenuByCode($group->code);
        //     Cache::put('navigation-menu-' . $group->code, $value);
        //     Log::channel('dashboard')->info('update store menu [' . $group->code . ']');
        // }
    }
}
