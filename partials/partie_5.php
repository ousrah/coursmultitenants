<!-- =================================================================== -->
<!-- PART 5: DYNAMIC THEMES PER TENANT -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Part 5: Dynamic Themes per Tenant</h2>

<!-- ========== CHAPTER 9: CONFIGURING VITE.JS AND THEME HELPERS ========== -->
<section id="themes-vite" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 9: Configuring Vite.js and Theme Helpers</h3>
    <p class="text-gray-700 mb-6">To offer deep customization, we will allow each tenant to have their own theme. The first step is to configure our asset bundler, Vite.js.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">9.1. Update `vite.config.js`</h4>
            <p class="text-gray-700 mb-4">We need to list the entry points (JS/CSS files) for each theme in Vite's configuration file.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-javascript">import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Central theme
                'resources/css/app.css',
                'resources/js/app.js',
                
                // Theme Lineone 1
                'resources/views/themes/lineone1/assets/css/app.css',
                'resources/views/themes/lineone1/assets/js/app.js',

                // Theme Lineone 2
                'resources/views/themes/lineone2/assets/css/app.css',
                'resources/views/themes/lineone2/assets/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">9.2. Create a `theme_vite()` helper</h4>
            <p class="text-gray-700 mb-4">To load the correct assets in our views, we create a custom helper function. Create the <code>app/helpers.php</code> file and register it in <code>composer.json</code> to be autoloaded.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

use App\Services\ThemeManager;
use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;

if (! function_exists('theme_vite')) {
    function theme_vite(string|array $entrypoints): HtmlString
    {
        $themeSlug = app(ThemeManager::class)->getCurrentThemeSlug();
        if (!$themeSlug) {
            return new HtmlString('');
        }

        $projectRelativeEntrypoints = collect($entrypoints)->map(function ($entrypoint) use ($themeSlug) {
            return 'resources/views/themes/' . $themeSlug . '/' . ltrim($entrypoint, '/');
        })->all();
        
        return app(Vite::class)($projectRelativeEntrypoints);
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>

<!-- ========== CHAPTER 10: DYNAMIC PROVIDER AND LAYOUT COMPONENTS ========== -->
<section id="themes-service-provider" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 10: Dynamic Provider and Layout Components</h3>
    <p class="text-gray-700 mb-6">The brain of our theming system will be a `ThemeManager` and a `ThemeServiceProvider`. They will determine which theme to load and configure Blade accordingly.</p>

    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">10.1. The `ThemeManager` Service</h4>
            <p class="text-gray-700 mb-4">This service's sole responsibility is to determine the current theme's slug based on a tenant's configuration.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ThemeManager
{
    public function getCurrentThemeSlug(): string
    {
        if (!tenancy()->initialized || !tenancy()->tenant) {
            return 'lineone1'; // Default theme for central app or if no tenant
        }

        $cacheKey = 'tenant_theme_' . tenancy()->tenant->getTenantKey();

        return Cache::rememberForever($cacheKey, function () {
            $themeSetting = Setting::where('key', 'theme')->first()?->value;
            return $themeSetting ?? 'lineone1';
        });
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">10.2. The `ThemeServiceProvider`</h4>
            <p class="text-gray-700 mb-4">This provider uses the `ThemeManager` to tell Laravel where to find the active theme's Blade views and components. Don't forget to register it in <code>bootstrap/providers.php</code>.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\Providers;

use App\Services\ThemeManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, function () {
            return new ThemeManager();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $themeComponentPath = resource_path('views/themes/lineone1/components');
            if (is_dir($themeComponentPath)) {
                Blade::anonymousComponentPath($themeComponentPath, 'theme');
            }
            return;
        }

        View::composer('*', function ($view) {
            $themeManager = $this->app->make(ThemeManager::class);
            $currentTheme = $themeManager->getCurrentThemeSlug();
            $themeViewPath = resource_path('views/themes/' . $currentTheme);
            $themeComponentPath = resource_path('views/themes/' . $currentTheme . '/components');

            if (is_dir($themeViewPath)) {
                $this->app['view']->prependLocation($themeViewPath);
            }

            if (is_dir($themeComponentPath)) {
                Blade::anonymousComponentPath($themeComponentPath, 'theme');
            }
        });
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">10.3. Layout Components</h4>
            <p class="text-gray-700 mb-4">Finally, components like `AppLayout` and `BaseLayout` must be modified to use the `ThemeManager` to render the layout view corresponding to the active theme.</p>
             <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">&lt;?php

namespace App\View\Components;

use App\Services\ThemeManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

class AppLayout extends Component
{
    protected ThemeManager $themeManager;
    protected string $themeSlug;

    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
        $this->themeSlug = $this->themeManager->getCurrentThemeSlug();
    }

    public function render()
    {
        $themePath = resource_path('views/themes/' . $this->themeSlug);
        $themeViewsPath = $themePath . '/views';
        $themeComponentsPath = $themePath . '/components';

        if (is_dir($themeViewsPath)) {
            view()->getFinder()->prependLocation($themeViewsPath);
        }

        if (is_dir($themeComponentsPath)) {
            Blade::anonymousComponentPath($themeComponentsPath, 'theme');
        }
        
        return view('layouts.app');
    }
}</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>