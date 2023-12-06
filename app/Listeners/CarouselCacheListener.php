<?php

namespace App\Listeners;

use App\Events\CarouselEvents;
use App\Models\Carousel;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

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
    public function handle(CarouselEvents $event)
    {
        Cache::forget("carousels");
        Cache::forget("carousel");

        Cache::rememberForever(empty($event->currCarousel) ? "carousels" : "carousel", function () use ($event) {
            $filter['current'] = $event->currCarousel;
            return $this->carouselModel->carousels($filter);
        });
    }
}
