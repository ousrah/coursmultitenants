<!-- =================================================================== -->
<!-- PART 3: TENANT MANAGEMENT (CENTRAL APPLICATION) -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Part 3: Tenant Management (Central Application)</h2>

<!-- ========== CHAPTER 5: CREATING THE CRUD FOR TENANTS ========== -->
<section id="crud-tenants" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 5: Creating the CRUD for Tenants</h3>
    <p class="text-gray-700 mb-6">We will now build the interface in our central application to list, create, edit, and delete tenants. This includes the controller and the Blade views.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.1. The Controller: TenantController.php</h4>
            <p class="text-gray-700 mb-4">This controller handles the business logic for the tenant CRUD, including search, sorting, pagination, and Excel export.</p>
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

        $tenant = Tenant::create([
            'id' => $validated['id'],
            'data' => [
                'name' => $validated['data']['name'],
                'plan' => $validated['data']['plan'] ?? 'Standard',
            ],
        ]);
        
        $tenant->createDomain(['domain' => $validated['domain']]);

        return redirect()->route('tenants.index')->with('success', 'Tenant created successfully.');
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
        
        return redirect()->route('tenants.index')->with('success', 'Tenant updated successfully.');
    }
    
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function export()
    {
        return Excel::download(new TenantsExport, 'tenants.xlsx');
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">5.2. The Blade Views</h4>
            <p class="text-gray-700 mb-4">We need several views for the CRUD: one for the list (<code>index.blade.php</code>), one for creation (<code>create.blade.php</code>), one for editing (<code>edit.blade.php</code>), and a partial form (<code>_form.blade.php</code>).</p>
            <p class="text-gray-700 mb-2"><b><code>resources/views/tenants/index.blade.php</code>:</b></p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-html">&lt;x-app-layout title="Tenant Management"&gt;
    &lt;div class="py-12 px-4 sm:px-6 lg:px-8"&gt;
        &lt;div class="max-w-7xl mx-auto" x-data="columnsManager"&gt;

            &lt;div class="sm:flex sm:items-center sm:justify-between mb-6"&gt;
                &lt;div&gt;
                    &lt;h2 class="text-2xl font-bold text-gray-900 dark:text-gray-200"&gt;Tenant Management&lt;/h2&gt;
                    &lt;p class="mt-1 text-sm text-gray-500"&gt;Manage client accounts (tenants) and their access domains.&lt;/p&gt;
                &lt;/div&gt;
                &lt;div class="mt-4 sm:mt-0 sm:ml-4 flex space-x-3 items-center"&gt;
                     &lt;a href="{{ route('tenants.create') }}" class="btn bg-primary text-white"&gt;New Tenant&lt;/a&gt;
                     &lt;a href="{{ route('tenants.export') }}" class="btn bg-success text-white"&gt;Export Excel&lt;/a&gt;
                 &lt;/div&gt;
            &lt;/div&gt;

            &lt;!-- Table, filters, pagination... --&gt;
            
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/x-app-layout&gt;</code></pre>
                <button class="copy-btn">Copy</button>
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
    @unless(isset($tenant))
    &lt;div class="md:col-span-2"&gt;
        &lt;label for="id" class="block text-sm font-medium text-gray-700"&gt;Tenant ID&lt;/label&gt;
        &lt;input type="text" name="id" id="id" value="{{ old('id') }}" class="mt-1 block w-full" placeholder="e.g., acme, companyx" required&gt;
        &lt;p class="mt-2 text-xs text-gray-500"&gt;Must be unique, no spaces or special characters (except dashes).&lt;/p&gt;
    &lt;/div&gt;
    @endunless

    &lt;div&gt;
        &lt;label for="data_name" class="block text-sm font-medium text-gray-700"&gt;Company Name&lt;/label&gt;
        &lt;input type="text" name="data[name]" id="data_name" value="{{ old('data.name', isset($tenant) ? $tenant->data['name'] ?? '' : '') }}" class="mt-1 block w-full" required&gt;
    &lt;/div&gt;

    &lt;div&gt;
        &lt;label for="domain" class="block text-sm font-medium text-gray-700"&gt;Main Domain&lt;/label&gt;
        &lt;input type="text" name="domain" id="domain" value="{{ old('domain', isset($tenant) ? $tenant->domains->first()?->domain ?? '' : '') }}" class="mt-1 block w-full" placeholder="e.g., client.mydomain.com" required&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPTER 6: AUTOMATING DATABASE CREATION ========== -->
<section id="auto-db-creation" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 6: Automating Database Creation</h3>
    <p class="text-gray-700 mb-6">When we create a tenant through our interface, its database, tables, and initial data should be created automatically in the background. We'll use Laravel's events and jobs for this.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.1. The `SetupTenantDatabaseListener`</h4>
            <p class="text-gray-700 mb-4">This listener will listen for the <code>TenantCreated</code> event and delegate the heavy lifting to a queued job.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
namespace App\Listeners;

use Stancl\Tenancy\Events\TenantCreated;
use App\Jobs\SetupTenantDatabase;

class SetupTenantDatabaseListener
{
    public function handle(TenantCreated $event)
    {
        $tenantModel = $event->tenant;
        if ($tenantModel->database_initialized) {
            return; // already processed
        }
        SetupTenantDatabase::dispatch($tenantModel);
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.2. The `SetupTenantDatabase` Job</h4>
            <p class="text-gray-700 mb-4">This job is responsible for running migrations and seeders for the new tenant.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\Jobs;

use Stancl\Tenancy\Jobs\MigrateDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\tenant\TenantDatabaseSeeder;

class SetupTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        $tenantId = $this->tenant->id;

        // 1. Migration
        MigrateDatabase::dispatchSync($this->tenant);

        // 2. Initialize tenant context
        tenancy()->initialize($this->tenant);

        // 3. Run Seeder
        try {
            Log::info("Job SeedTenantDatabase started for: {$tenantId}");
            (new TenantDatabaseSeeder())->run();
            Log::info("Seeding completed for: {$tenantId}");
        } catch (\Exception $e) {
            Log::error("Seeding job failed for {$tenantId}: " . $e->getMessage());
            throw $e;
        }

        // 4. End tenant context
        tenancy()->end();
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">6.3. Event Registration and Configuration</h4>
            <p class="text-gray-700 mb-4">We register the listener in `AppServiceProvider` and configure the central domains for production.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php
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
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 mt-4">Don't forget to add your production domain to the central domains list in <code>config/tenancy.php</code> to prevent it from being treated as a tenant.</p>
             <div class="code-block-wrapper mt-4">
                <pre class="code-block"><code class="language-php">// config/tenancy.php
'central_domains' => [ 
    '127.0.0.1',
    'localhost',
    'central.manar.com', // Very important for production
],</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>