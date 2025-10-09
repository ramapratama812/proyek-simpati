<?php
namespace App\Http; 
class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        // middleware lain ...
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];

}
