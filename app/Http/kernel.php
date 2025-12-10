<?php

namespace App\Http;

use Illuminate\Console\Scheduling\Schedule;
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

    protected function schedule(Schedule $schedule)
    {
        // Senin jam 03:00 -> agregasi data internal
        $schedule->command('dosen:aggregate-metrics')
            ->weeklyOn(1, '03:00'); // 1 = Monday

        // Senin jam 03:15 -> sync SINTA
        $schedule->command('dosen:sync-sinta-metrics')
            ->weeklyOn(1, '03:15');


        // panggil command "php artisan schedule:run" di server supaya scheduler bisa jalan
    }
}
