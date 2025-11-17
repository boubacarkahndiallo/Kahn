<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        // ... autres middlewares
        'check.client.access' => \App\Http\Middleware\CheckClientAccess::class,
    ];
}
