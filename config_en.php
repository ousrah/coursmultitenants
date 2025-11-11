<?php
// General course configuration
define('COURSE_TITLE', 'Building a Laravel 12 Multi-Tenant, Dynamic Theming and Modular Application from A to Z');
define('COURSE_AUTHOR', 'F. Rahmouni Oussama');
define('COURSE_LAST_UPDATE', 'November 2025');

// Course structure to dynamically generate the table of contents
$course_parts = [
    "Part 1: Project Initialization & Prerequisites" => [
        ['id' => 'install-laravel', 'title' => "Chapter 1: Installing Laravel 12 and the Base Layout"],
        ['id' => 'config-redis', 'title' => "Chapter 2: Integrating Redis for Cache & Sessions"]
    ],
    "Part 2: Multi-Tenant Architecture" => [
        ['id' => 'install-tenancy', 'title' => "Chapter 3: Setting up Stancl/Tenancy"],
        ['id' => 'tenancy-migrations-routes', 'title' => "Chapter 4: Tenant Migrations, Seeders, and Routing"]
    ],
    "Part 3: Tenant Management" => [
        ['id' => 'crud-tenants', 'title' => "Chapter 5: Creating the CRUD for Tenants"],
        ['id' => 'auto-db-creation', 'title' => "Chapter 6: Automating Database Creation"]
    ],
    "Part 4: Application Modularization" => [
        ['id' => 'install-modules', 'title' => "Chapter 7: Integrating Laravel-Modules (Nwidart)"],
        ['id' => 'modules-migrations-routes', 'title' => "Chapter 8: Migrations and Routes in Modules"]
    ],
    "Part 5: Dynamic Theming per Tenant" => [
        ['id' => 'themes-vite', 'title' => "Chapter 9: Configuring Vite.js and Theme Helpers"],
        ['id' => 'themes-service-provider', 'title' => "Chapter 10: Dynamic Provider and Layout Components"]
    ]
];
?>