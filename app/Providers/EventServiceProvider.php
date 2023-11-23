<?php

namespace App\Providers;

use App\Events\BrandEvents;
use App\Events\CarouselEvents;
use App\Events\CategoryEvents;
use App\Events\ProductEvents;
use App\Listeners\BrandCacheListener;
use App\Listeners\CarouselCacheListener;
use App\Listeners\CategoryCacheListener;
use App\Listeners\ProductCacheListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductEvents::class => [
            ProductCacheListener::class,
        ],
        CategoryEvents::class => [
            CategoryCacheListener::class,
        ],
        BrandEvents::class => [
            BrandCacheListener::class
        ],
        CarouselEvents::class => [
            CarouselCacheListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
