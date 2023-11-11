<?php

namespace App\Listeners;

use App\Events\ProductEvents;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ProductCacheListener
{
    private $productModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductEvents  $event
     * @return void
     */
    public function handle(ProductEvents $event)
    {
        Cache::forget("products");

        Cache::rememberForever("products", function () {
            return $this->productModel->products();
        });
    }
}
