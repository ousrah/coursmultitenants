<!-- =================================================================== -->
<!-- PART 4: APPLICATION MODULARIZATION -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Part 4: Application Modularization</h2>

<!-- ========== CHAPTER 7: INTEGRATING LARAVEL-MODULES (NWIDART) ========== -->
<section id="install-modules" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 7: Integrating Laravel-Modules (Nwidart)</h3>
    <p class="text-gray-700 mb-6">For large applications, it's better to split the code into modules (e.g., Billing, Schooling). We use the <code>nwidart/laravel-modules</code> package to achieve this.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.1. Installation and Publishing</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require nwidart/laravel-modules
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.2. Configure composer.json</h4>
            <p class="text-gray-700 mb-4">Add the `merge-plugin` so that Composer automatically discovers each module's dependencies.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-json">"extra": {
    "laravel": {
        "dont-discover": []
    },
    "merge-plugin": {
        "include": [
            "Modules/*/composer.json"
        ]
    }
},</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.3. Create a First Module</h4>
            <p class="text-gray-700 mb-4">This command will create a <code>Modules/School</code> directory with a full file structure (Controllers, Models, Views, etc.).</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan module:make School</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.4. Reload Composer's Autoloader</h4>
            <p class="text-gray-700 mb-4">After creating a module, it's crucial to update the class autoloader.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer dump-autoload
php artisan optimize:clear</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPTER 8: MIGRATIONS AND ROUTES IN MODULES ========== -->
<section id="modules-migrations-routes" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 8: Migrations and Routes in Modules</h3>
    <p class="text-gray-700 mb-6">Working with modules slightly changes how we manage migrations and routes, especially in a multi-tenant context.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.1. Tenant Migrations in a Module</h4>
            <p class="text-gray-700 mb-4">For <code>tenancy</code> to automatically discover our modules' tenant migrations, we need to update <code>config/tenancy.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// in config/tenancy.php
'migration_parameters' => [
    '--force' => true,
    '--path' => [
        'database/migrations/tenant',
        'Modules/*/database/migrations/tenant' // Add this line
    ],
    '--realpath' => true,
],</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 mt-4">Then, to create a tenant migration for a module, specify the path:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:migration create_test_table --path="Modules/School/database/migrations/tenant"</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.2. Tenant Seeders in a Module</h4>
            <p class="text-gray-700 mb-4">Your main tenant seeder (e.g., `TenantDatabaseSeeder.php`) can call seeders from each module.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Manar',
            'email' => 'manar@admin.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        // Call module seeders
         $this->call(\Modules\School\Database\Seeders\SchoolDatabaseSeeder::class);
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.3. Module Routes and Tenant Context</h4>
            <p class="text-gray-700 mb-4">To make a module's routes aware of the current tenant, create an alias for the `tenancy` middleware in <code>bootstrap/app.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // in bootstrap/app.php
// ...
->withMiddleware(function (Middleware $middleware) {
     $middleware->alias([
        'tenant' => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    ]);
    // ...
})
// ...</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 mt-4">Then, in your module's routes file (e.g., <code>Modules/School/routes/web.php</code>), you can protect your routes like this:</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;

Route::middleware(['web', 'tenant', 'auth'])->prefix('admin')->group(function () {
    Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
});</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>