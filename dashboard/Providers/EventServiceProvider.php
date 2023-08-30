<?php

namespace Dashboard\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 'Dashboard\Events\ArticleUpdated' => [
        //     'Dashboard\Listeners\ArticleUpdatedHook',
        // ],
        'Dashboard\Events\MenuUpdatedEvent' => [
            'Dashboard\Listeners\MenuUpdatedListener',
        ],
        'Dashboard\Events\MenuDeletedEvent' => [
            'Dashboard\Listeners\MenuDeletedListener',
        ],
        // 'Dashboard\Events\SettingDeleted' => [
        //     'Dashboard\Listeners\SettingDeletedHook',
        // ],
        // 'Dashboard\Events\SettingGroupUpdated' => [
        //     'Dashboard\Listeners\SettingGroupUpdatedHook',
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
