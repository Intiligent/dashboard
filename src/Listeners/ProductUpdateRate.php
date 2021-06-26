<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\ProductUpdated;
use App\Models\Product;

class ProductUpdateRate
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
     * @param  ProductUpdated  $event
     * @return void
     */
    public function handle(ProductUpdated $event)
    {
        $this->updateRate($event->product);
    }

    /**
     * Обновление рейтинга
     *
     * @param  ProductUpdated  $event
     * @return void
     */
    public function updateRate(Product $product)
    {
        $rate = 0;
        $product->load(['category', 'image']);

        if ($product->image) {
            $rate += 50;
        }
        if ($product->price) {
            $rate += 30;
        }
        if ($product->description) {
            $rate += 5;
        }
        if ($product->category && $product->category->slug !== 'other') {
            $rate += 5;
        }
        if ($product->rate !== $rate) {
            $product->rate = $rate;
            $product->save();
        }
    }
}
