<!-- =================================================================== -->
<!-- PARTIE 2 : ARCHITECTURE MULTI-TENANT -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 2 : Architecture Multi-Tenant</h2>

<!-- ========== CHAPITRE 3 : MISE EN PLACE DE STANCL/TENANCY ========== -->
<section id="install-tenancy" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 3 : Mise en place de Stancl/Tenancy</h3>
    <p class="text-gray-700 mb-6">Nous installons le package <code>stancl/tenancy</code>, qui va nous fournir tous les outils pour gérer des bases de données séparées pour chaque client (tenant).</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.1. Installation du package</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require stancl/tenancy
php artisan tenancy:install</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.2. Création du modèle Tenant</h4>
            <p class="text-gray-700 mb-4">Créez le fichier <code>app/Models/Tenant.php</code>. Ce modèle Eloquent représentera nos clients dans la base de données centrale.</p>
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
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">3.3. Configuration et migration centrale</h4>
            <p class="text-gray-700 mb-4">Ouvrez <code>config/tenancy.php</code> et assurez-vous que la clé <code>tenant_model</code> pointe vers votre nouveau modèle. Ensuite, lancez la migration pour créer la table des tenants.</p>
             <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// In config/tenancy.php
'tenant_model' => \App\Models\Tenant::class,</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <div class="code-block-wrapper mt-4">
                <pre class="code-block"><code class="language-bash">php artisan migrate</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPITRE 4 : MIGRATIONS, SEEDERS ET ROUTAGE TENANT ========== -->
<section id="tenancy-migrations-routes" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 4 : Migrations, Seeders et Routage Tenant</h3>
    <p class="text-gray-700 mb-6">Nous allons maintenant structurer notre application pour gérer les migrations et les routes spécifiques à chaque tenant.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.1. Migrations des Tenants</h4>
            <p class="text-gray-700 mb-4">Les migrations pour les tables des tenants (ex: produits, factures...) doivent être placées dans <code>database/migrations/tenant</code>. Pour en créer une :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:migration create_eleves_table --path=database/migrations/tenant</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4">Pour exécuter ces migrations sur tous les tenants existants ou sur un tenant spécifique :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash"># Pour tous les tenants
php artisan tenants:migrate

# Pour un seul tenant
php artisan tenants:migrate --tenant=ecole1

# Pour réinitialiser et ré-exécuter les migrations pour tous les tenants
php artisan tenants:migrate-fresh</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.2. Seeders des Tenants</h4>
            <p class="text-gray-700 mb-4">Pour que les commandes de seeding ciblent la bonne classe, configurez <code>config/tenancy.php</code> :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">// Dans config/tenancy.php
'seeder_parameters' => [
    '--class' => 'TenantDatabaseSeeder', // root seeder class
],</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
             <p class="text-gray-700 mt-4">Ensuite, lancez le seeding :</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan tenants:seed</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.3. Créer et configurer le TenancyServiceProvider</h4>
            <p class="text-gray-700 mb-4">Ce Service Provider, fourni par le package, est crucial. Assurez-vous qu'il est bien créé (<code>app/Providers/TenancyServiceProvider.php</code>) et enregistré dans <code>bootstrap/providers.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

// Dans bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // Ajoutez cette ligne
];</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.4. Séparation des routes</h4>
            <p class="text-gray-700 mb-4">Le fichier <code>routes/tenant.php</code> contiendra les routes accessibles via un domaine de tenant. Le fichier <code>routes/web.php</code> est pour l'application centrale.</p>
            <p class="text-gray-700 mb-2"><b>Exemple pour <code>routes/tenant.php</code>:</b></p>
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
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4 mb-2"><b>Exemple pour <code>routes/web.php</code>:</b></p>
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
    
    // Ajout des routes pour le CRUD des tenants
    Route::resource('tenants', \App\Http\Controllers\TenantController::class)->except(['show']);
});</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">4.5. Créer un premier Tenant pour tester</h4>
            <p class="text-gray-700 mb-4">Utilisons Tinker pour créer manuellement notre premier tenant.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan tinker

# Dans Tinker
use App\Models\Tenant;
$tenant = Tenant::create(['id' => 'ecole1']);
$tenant->domains()->create(['domain' => 'ecole1.manar.com']);
exit;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
             <p class="text-gray-700 mt-4">N'oubliez pas d'ajouter <code>127.0.0.1 ecole1.manar.com</code> à votre fichier <code>hosts</code> pour pouvoir y accéder localement via `http://ecole1.manar.com:8000`.</p>
        </div>
    </div>
</section>