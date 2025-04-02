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
        // Allow API endpoints without CSRF if needed
        // 'api/*',
        // For debugging purposes, you might temporarily disable CSRF protection for these routes
        // '/films/*',
        // '/actors/*',
        // '/categories/*',
    ];
}
