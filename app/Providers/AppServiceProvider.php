<?php

namespace App\Providers;

use App\Services\Notification\ChannelDispatcher;
use App\Services\Notification\Contracts\ChannelDispatcherInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ChannelDispatcherInterface::class,
            ChannelDispatcher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
