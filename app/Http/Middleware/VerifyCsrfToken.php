<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
     protected $except = [
        'telescope-api/*',
        'telescope/telescope-api/*', // dashboard makes calls under /telescope/...
        'telescope/*',               // belt-and-suspenders
    ];
}
