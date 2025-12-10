<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        // middleware lain ...
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
