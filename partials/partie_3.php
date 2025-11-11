<!-- =================================================================== -->
<!-- PARTIE 3 : GESTION DES TENANTS (APPLICATION CENTRALE) -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 3 : Gestion des Tenants (Application Centrale)</h2>

<!-- ========== CHAPITRE 5 : CRÉATION DU CRUD POUR LES TENANTS ========== -->
<section id="crud-tenants" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 5 : Création du CRUD pour les Tenants</h3>
    <p class="text-gray-700 mb-6">Nous construisons l'interface dans notre application centrale pour lister, créer, modifier et supprimer des tenants. Cela inclut le contrôleur et les vues Blade.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.1. Le contrôleur : TenantController.php</h4>
            <p class="text-gray-700 mb-4">Ce contrôleur gère la logique pour le CRUD des tenants, incluant la recherche, le tri, la pagination et l'export Excel.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Exports\TenantsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search'   => $request->input('search', ''),
            'sort_by'  => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
            'per_page' => $request->input('per_page', 10),
        ];

        $query = Tenant::with('domains');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('id', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('data->name', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('domains', function ($domainQuery) use ($filters) {
                      $domainQuery->where('domain', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        
        $query->orderBy($filters['sort_by'], $filters['sort_dir']);
        
        $tenants = $query->paginate($filters['per_page'])->withQueryString();

        return view('tenants.index', compact('tenants', 'filters'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:tenants|alpha_dash|min:3',
            'domain' => ['required', 'string', 'unique:domains,domain', 'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'data' => 'required|array',
            'data.name' => 'required|string|max:255',
            'data.plan' => 'nullable|string',
        ]);

        $tenant = Tenant::create($validated);
        $tenant->createDomain(['domain' => $validated['domain']]);

        return redirect()->route('tenants.index')->with('success', 'Tenant créé avec succès.');
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'domain' => [
                'required', 'string',
                Rule::unique('domains')->ignore($tenant->domains->first()?->id),
                'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'data' => 'required|array',
            'data.name' => 'required|string|max:255',
            'data.plan' => 'nullable|string',
        ]);

        $tenant->update(['data' => $validated['data']]);
        
        if ($tenant->domains->first()) {
            $tenant->domains->first()->update(['domain' => $validated['domain']]);
        } else {
            $tenant->createDomain(['domain' => $validated['domain']]);
        }
        
        return redirect()->route('tenants.index')->with('success', 'Tenant mis à jour avec succès.');
    }
    
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant supprimé avec succès.');
    }

    public function export()
    {
        return Excel::download(new TenantsExport, 'tenants.xlsx');
    }
}</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.2. Les Vues Blade</h4>
            <p class="text-gray-700 mb-4">Nous avons besoin de plusieurs vues pour le CRUD : une pour la liste (<code>index.blade.php</code>), une pour la création (<code>create.blade.php</code>), une pour l'édition (<code>edit.blade.php</code>), et un formulaire partiel (<code>_form.blade.php</code>).</p>
            <p class="text-gray-700 mb-2"><b><code>resources/views/tenants/index.blade.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-html">&lt;x-app-layout title="Gestion des Tenants"&gt;
    &lt;!-- Code complet de la vue index.blade.php --&gt;
&lt;/x-app-layout&gt;
@push('scripts')
&lt;script&gt;
document.addEventListener('alpine:init', () => {
    Alpine.data('columnsManager', () => ({
        // ... Code Alpine.js complet ...
    }));
});
&lt;/script&gt;
@endpush</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4 mb-2"><b><code>resources/views/tenants/create.blade.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-html">&lt;x-app-layout title="Créer un Tenant"&gt;
    &lt;div class="py-12 px-4 sm:px-6 lg:px-8"&gt;
        &lt;div class="max-w-4xl mx-auto"&gt;
            &lt;div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg"&gt;
                &lt;div class="p-6 md:p-8"&gt;
                    &lt;h2 class="text-2xl font-bold text-gray-900 dark:text-gray-200"&gt;
                        Créer un nouveau tenant
                    &lt;/h2&gt;

                    &lt;form action="{{ route('tenants.store') }}" method="POST" class="mt-8"&gt;
                        @csrf
                        @include('tenants._form')

                        &lt;div class="flex items-center justify-end space-x-4 mt-8"&gt;
                            &lt;a href="{{ route('tenants.index') }}" class="btn bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white"&gt;Annuler&lt;/a&gt;
                            &lt;button type="submit" class="btn bg-primary text-white"&gt;Créer le Tenant&lt;/button&gt;
                        &lt;/div&gt;
                    &lt;/form&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/x-app-layout&gt;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4 mb-2"><b><code>resources/views/tenants/_form.blade.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-html">@if ($errors->any())
    &lt;div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"&gt;
        &lt;ul&gt;
            @foreach ($errors->all() as $error)
                &lt;li&gt;{{ $error }}&lt;/li&gt;
            @endforeach
        &lt;/ul&gt;
    &lt;/div&gt;
@endif

&lt;div class="grid grid-cols-1 md:grid-cols-2 gap-6"&gt;
    &lt;!-- ID du Tenant (uniquement à la création) --&gt;
    @unless(isset($tenant))
    &lt;div class="md:col-span-2"&gt;
        &lt;label for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300"&gt;ID du Tenant&lt;/label&gt;
        &lt;input type="text" name="id" id="id" value="{{ old('id') }}"
               class="mt-1  p-2  block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
               placeholder="ex: acme, societex" required&gt;
        &lt;p class="mt-2 p-2  text-xs text-gray-500"&gt;Doit être unique, sans espaces ni caractères spéciaux (sauf tirets).&lt;/p&gt;
    &lt;/div&gt;
    @endunless

    &lt;!-- Nom de l'entreprise (dans data) --&gt;
    &lt;div&gt;
        &lt;label for="data_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"&gt;Nom de l'entreprise&lt;/label&gt;
        &lt;input type="text" name="data[name]" id="data_name" value="{{ old('data.name', isset($tenant) ? $tenant->data['name'] ?? '' : '') }}"
               class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" required&gt;
    &lt;/div&gt;

    &lt;!-- Domaine principal --&gt;
    &lt;div&gt;
        &lt;label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300"&gt;Domaine principal&lt;/label&gt;
        &lt;input type="text" name="domain" id="domain" value="{{ old('domain', isset($tenant) ? $tenant->domains->first()?->domain ?? '' : '') }}"
               class="mt-1 p-2  block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
               placeholder="ex: client.mondomaine.com" required&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPITRE 6 : AUTOMATISATION DE LA CRÉATION DES BDD ========== -->
<section id="auto-db-creation" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 6 : Automatisation de la création des BDD</h3>
    <p class="text-gray-700 mb-6">Lorsque nous créons un tenant via notre interface, sa base de données, ses tables et ses données initiales doivent être créées automatiquement en arrière-plan. Nous utilisons les événements et les jobs de Laravel pour cela.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.1. Le Listener `SetupTenantDatabaseListener`</h4>
            <p class="text-gray-700 mb-4">Ce listener écoute l'événement <code>TenantCreated</code> et délègue le travail à un job en file d'attente.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // app/Listeners/SetupTenantDatabaseListener.php
namespace App\Listeners;

use Stancl\Tenancy\Events\TenantCreated;
use App\Jobs\SetupTenantDatabase;

class SetupTenantDatabaseListener
{
    public function handle(TenantCreated $event)
    {
        $tenantModel = $event->tenant;
        if ($tenantModel->database_initialized) {
            return; // déjà traité
        }
        SetupTenantDatabase::dispatch($tenantModel);
    }
}</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.2. Le Job `SetupTenantDatabase`</h4>
            <p class="text-gray-700 mb-4">Ce job est responsable de l'exécution des migrations et des seeders pour le nouveau tenant.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // app/Jobs/SetupTenantDatabase.php
namespace App\Jobs;

use Stancl\Tenancy\Jobs\MigrateDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Database\Seeders\tenant\TenantDatabaseSeeder;

class SetupTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        // 1. Migration
        MigrateDatabase::dispatchSync($this->tenant);

        // 2. Initialiser le contexte du tenant
        tenancy()->initialize($this->tenant);

        // 3. Lancer le Seeder
        (new TenantDatabaseSeeder())->run();

        // 4. Terminer le contexte
        tenancy()->end();
    }
}</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.3. Enregistrement de l'événement et configuration</h4>
            <p class="text-gray-700 mb-4">Nous enregistrons le listener dans <code>AppServiceProvider</code> et nous configurons les domaines centraux pour la production.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php // app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenantCreated;
use App\Listeners\SetupTenantDatabaseListener;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            TenantCreated::class,
            SetupTenantDatabaseListener::class,
        );
    }
}</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 mt-4">N'oubliez pas d'ajouter votre domaine de production à la liste des domaines centraux dans <code>config/tenancy.php</code> pour éviter qu'il soit traité comme un tenant.</p>
             <div class="code-block-wrapper mt-4">
                <pre class="code-block"><code class="language-php">// config/tenancy.php
'central_domains' => [ 
    '127.0.0.1',
    'localhost',
    'central.manar.com', // Très important pour la production
],</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>