<!-- =================================================================== -->
<!-- PART 2: MULTI-TENANT ARCHITECTURE -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Part 2: Multi-Tenant Architecture</h2>

<!-- ========== CHAPTER 3: SETTING UP STANCL/TENANCY ========== -->
<section id="install-tenancy" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 3: Setting up Stancl/Tenancy</h3>
    <p class="text-gray-700 mb-6">We install the <code>stancl/tenancy</code> package, which will provide us with all the tools to manage separate databases for each client (tenant).</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.1. Install the Package</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require stancl/tenancy
php artisan tenancy:install</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.2. Create the Tenant Model</h4>
            <p class="text-gray-700 mb-4">Create the file <code>app/Models/Tenant.php</code>. This Eloquent model will represent our clients in the central database.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = ['id', 'data'];
    protected $casts = [
        'data' => 'array',
    ];
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.3. Configuration and Central Migration</h4>
            <p class="text-gray-700 mb-4">Open <code>config/tenancy.php</code> and ensure the <code>tenant_model</code> key points to your new model. Then, run the migration to create the tenants table.</p>
             <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// In config/tenancy.php
'tenant_model' => \App\Models\Tenant::class,</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <div class="code-block-wrapper mt-4">
                <pre class="code-block"><code class="language-bash">php artisan migrate</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPTER 4: TENANT MIGRATIONS, SEEDERS, AND ROUTING ========== -->
<section id="tenancy-migrations-routes" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 4: Tenant Migrations, Seeders, and Routing</h3>
    <p class="text-gray-700 mb-6">We will now structure our application to handle migrations and routes specific to each tenant.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.1. Tenant Migrations</h4>
            <p class="text-gray-700 mb-4">Migrations for tenant tables (e.g., products, invoices...) must be placed in <code>database/migrations/tenant</code>. To create one:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:migration create_eleves_table --path=database/migrations/tenant</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 mt-4">To run these migrations on all existing tenants or on a specific tenant:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash"># For all tenants
php artisan tenants:migrate

# For a single tenant
php artisan tenants:migrate --tenant=school1

# To reset and re-run migrations for all tenants
php artisan tenants:migrate-fresh</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.2. Tenant Seeders</h4>
            <p class="text-gray-700 mb-4">To ensure seeding commands target the correct class, configure <code>config/tenancy.php</code>:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// In config/tenancy.php
'seeder_parameters' => [
    '--class' => 'TenantDatabaseSeeder', // root seeder class
],</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
             <p class="text-gray-700 mt-4">Then, run the seeder:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan tenants:seed</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.3. Create and configure TenancyServiceProvider</h4>
            <p class="text-gray-700 mb-4">This Service Provider, included with the package, is crucial. Ensure it is created (<code>app/Providers/TenancyServiceProvider.php</code>) and registered in <code>bootstrap/providers.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

// In bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // Add this line
];</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.4. Route Separation</h4>
            <p class="text-gray-700 mb-4">The <code>routes/tenant.php</code> file will contain routes accessible via a tenant's domain. The <code>routes/web.php</code> file is for the central application.</p>
            <p class="text-gray-700 mb-2"><b>Example for <code>routes/tenant.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
// routes/tenant.php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\AuthController::class, 'loginView'])->name('login');
        Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [\App\Http\Controllers\PagesController::class, 'index'])->name('index');
    });
});</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 mt-4 mb-2"><b>Example for <code>routes/web.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
// routes/web.php
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/loginadmin', [\App\Http\Controllers\AuthController::class, 'loginadmin'])->name('loginadmin');
    Route::post('/loginadmin', [\App\Http\Controllers\AuthController::class, 'loginadminpost'])->name('loginadminpost');
});

Route::middleware('auth')->group(function () {
    Route::post('/logoutadmin', [\App\Http\Controllers\AuthController::class, 'logoutadmin'])->name('logoutadmin');
    Route::get('/indexadmin', [\App\Http\Controllers\PagesController::class, 'tenants'])->name('indexadmin');
    
    // Add routes for the tenant CRUD
    Route::resource('tenants', \App\Http\Controllers\TenantController::class)->except(['show']);
    Route::get('tenants/export', [\App\Http\Controllers\TenantController::class, 'export'])->name('tenants.export');
});</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.5. Create a First Tenant for Testing</h4>
            <p class="text-gray-700 mb-4">Let's use Tinker to manually create our first tenant.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan tinker

# In Tinker
use App\Models\Tenant;
$tenant = Tenant::create(['id' => 'school1']);
$tenant->domains()->create(['domain' => 'school1.myapp.com']);
exit;</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
             <p class="text-gray-700 mt-4">Don't forget to add <code>127.0.0.1 school1.myapp.com</code> to your <code>hosts</code> file to access it locally via `http://school1.myapp.com:8000`.</p>
        </div>
    </div>
</section>