<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // 🔐 EXISTING
        'auth'          => \App\Filters\AuthFilter::class,
        'guest'         => \App\Filters\GuestFilter::class,

        // 🔴 ADDED (ROLE & DEPARTMENT SECURITY)
        'permission'    => \App\Filters\PermissionFilter::class,
        'department'    => \App\Filters\DepartmentFilter::class,
    ];

    /**
     * List of special required filters.
     */
    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * Global filters applied to every request.
     */
    public array $globals = [
        'before' => [
            // 'csrf',
        ],
        'after' => [
            // 'secureheaders',
        ],
    ];

    public array $methods = [];

    /**
     * URI Pattern Filters
     */
    public array $filters = [
        // 1️⃣ Only logged-in users can access these
        'auth' => [
            'before' => [
                'team-builder',
                'team-builder/*',
            ]
        ],

        // 2️⃣ Logged-in users CANNOT access the guest-only pages
        'guest' => [
            'before' => [
                'auth/login',
                '/',
            ]
        ],
    ];
}
