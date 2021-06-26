<?php

namespace Dashboard\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Menu;

class MenuUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $menu;
    public $original;

    /**
     * Create a new event instance.
     *
     * @param Menu $menu
     * @return void
     */
    public function __construct(Menu $menu, Menu $original = null)
    {
        $this->menu = $menu;
        $this->original = $original;
    }
}
