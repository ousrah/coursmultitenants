<!-- =================================================================== -->
<!-- PARTIE 4 : MODULARISATION DE L'APPLICATION -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 4 : Modularisation de l'Application</h2>

<!-- ========== CHAPITRE 7 : INTÉGRATION DE LARAVEL-MODULES (NWIDART) ========== -->
<section id="install-modules" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 7 : Intégration de Laravel-Modules (Nwidart)</h3>
    <p class="text-gray-700 mb-6">Pour les grosses applications, il est préférable de diviser le code en modules (ex: Facturation, Scolarité). Nous utilisons le package <code>nwidart/laravel-modules</code> pour y parvenir.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.1. Installation et publication</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require nwidart/laravel-modules
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.2. Configuration de composer.json</h4>
            <p class="text-gray-700 mb-4">Ajoutez le plugin `merge-plugin` pour que Composer découvre automatiquement les dépendances de chaque module.</p>
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
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.3. Création d'un premier module</h4>
            <p class="text-gray-700 mb-4">Cette commande va créer un dossier <code>Modules/School</code> avec toute une arborescence (Contrôleurs, Modèles, Vues, etc.).</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan module:make School</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">7.4. Recharger l'autoload de Composer</h4>
            <p class="text-gray-700 mb-4">Après avoir créé un module, il est crucial de mettre à jour le chargement automatique des classes.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer dump-autoload
php artisan optimize:clear</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPITRE 8 : MIGRATIONS ET ROUTES DANS LES MODULES ========== -->
<section id="modules-migrations-routes" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 8 : Migrations et Routes dans les Modules</h3>
    <p class="text-gray-700 mb-6">Travailler avec des modules change la façon dont nous gérons les migrations et les routes, surtout dans un contexte multi-tenant.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.1. Migrations des tenants dans un module</h4>
            <p class="text-gray-700 mb-4">Pour que <code>tenancy</code> découvre automatiquement les migrations des tenants de nos modules, nous devons mettre à jour <code>config/tenancy.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// dans config/tenancy.php
'migration_parameters' => [
    '--force' => true,
    '--path' => [
        'database/migrations/tenant',
        'Modules/*/database/migrations/tenant' // Ajout de cette ligne
    ],
    '--realpath' => true,
],</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4">Ensuite, pour créer une migration de tenant pour un module, spécifiez le chemin :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:migration create_test_table --path="Modules/School/database/migrations/tenant"</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.2. Seeders des tenants dans un module</h4>
            <p class="text-gray-700 mb-4">Votre seeder principal de tenant (ex: `TenantDatabaseSeeder.php`) peut appeler les seeders de chaque module.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // database/seeders/TenantDatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Tenant',
            'email' => 'admin@tenant.com',
            'password' => bcrypt('password'),
        ]);

        // Appeler les seeders des modules
        $this->call(\Modules\School\Database\Seeders\SchoolDatabaseSeeder::class);
        // $this->call(\Modules\OtherModule\Database\Seeders\OtherModuleSeeder::class);
    }
}</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">8.3. Routes des modules et contexte du tenant</h4>
            <p class="text-gray-700 mb-4">Pour que les routes d'un module soient conscientes du tenant actuel, créez un alias pour le middleware de `tenancy` dans <code>bootstrap/app.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // dans bootstrap/app.php
// ...
->withMiddleware(function (Middleware $middleware) {
     $middleware->alias([
        'tenant' => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    ]);
    // ...
})
// ...</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4">Ensuite, dans le fichier de routes de votre module (ex: <code>Modules/School/routes/web.php</code>), protégez vos routes ainsi :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;

Route::middleware(['web', 'tenant', 'auth'])->prefix('admin')->group(function () {
    Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
});</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>