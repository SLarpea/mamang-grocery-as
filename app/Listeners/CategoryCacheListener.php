<?php

namespace App\Listeners;

use App\Events\CategoryEvents;
use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CategoryCacheListener
{
    private $categoryModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CategoryEvents  $event
     * @return void
     */
    public function handle(CategoryEvents $event)
    {
        Cache::forget("categories");

        Cache::rememberForever("categories", function () {
            return $this->categoryModel->categories();
        });
    }
}
