<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Listeners\CreateDosenAfterRegister;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Daftar event dan listener.
     */
    protected $listen = [
        Registered::class => [
            CreateDosenAfterRegister::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
