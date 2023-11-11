<?php

namespace App\Listeners;

use App\Models\Brand;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BrandCacheListener
{
    private $brandModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->brandModel = new Brand();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Cache::forget("brands");

        Cache::rememberForever("brands", function() {
            return $this->brandModel->brands();
        });
    }
}
