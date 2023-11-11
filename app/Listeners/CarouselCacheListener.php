<?php

namespace App\Listeners;

use App\Models\Carousel;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CarouselCacheListener
{
    private $carouselModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->carouselModel = new Carousel();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Cache::forget("carousels");

        Cache::rememberForever("carousels", function () {
            return $this->carouselModel->carousels();
        });
    }
}
