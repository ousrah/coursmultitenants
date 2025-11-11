<!-- =================================================================== -->
<!-- PART 1: PROJECT INITIALIZATION & PREREQUISITES -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Part 1: Project Initialization & Prerequisites</h2>

<!-- ========== GITHUB INFO BLOCK ========== -->
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-sm mb-12" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 9a.75.75 0 00-1.5 0v2.25H9a.75.75 0 000 1.5h2.25V15a.75.75 0 001.5 0v-2.25H15a.75.75 0 000-1.5h-2.25V9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">
                This course comes with a complete, open-source demo project. You can view, clone, or contribute to the source code directly on GitHub.
                <a href="https://www.github.com/ousrah/multitenants" target="_blank" rel="noopener noreferrer" class="font-bold underline hover:text-blue-600">
                    View the project on GitHub &rarr;
                </a>
            </p>
        </div>
    </div>
</div>

<!-- ========== CHAPTER 1: INSTALLING LARAVEL 12 AND THE BASE LAYOUT ========== -->
<section id="install-laravel" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 1: Installing Laravel 12 and the Base Layout</h3>
    <p class="text-gray-700 mb-6">We start with the basics: creating a fresh Laravel 12 project, configuring the database, and setting up a simple layout structure with a Blade component to get ready for building our interface.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.1. Create the Laravel Project</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer create-project laravel/laravel multitenants</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.2. Configure the Environment (.env)</h4>
            <p class="text-gray-700 mb-4">Modify your <code>.env</code> file to connect the project to your central MySQL database.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-ini">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_multitenant_central
DB_USERNAME=root
DB_PASSWORD=</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.3. Run the First Migration</h4>
            <p class="text-gray-700 mb-4">Run the initial migration to create Laravel's base tables (users, etc.) in the central database.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan migrate</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.4. Create the Layout Component</h4>
            <p class="text-gray-700 mb-4">We create an <code>AppLayout</code> component that will serve as the main structure for our application's pages.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:component AppLayout</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
            <p class="text-gray-700 my-4">This generates <code>App/View/Components/AppLayout.php</code> and <code>resources/views/components/app-layout.blade.php</code>. You can then structure your layout by including partials like a header and a sidebar (<code>main-sidebar.blade.php</code>).</p>
        </div>
    </div>
</section>

<!-- ========== CHAPTER 2: INTEGRATING REDIS ========== -->
<section id="config-redis" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapter 2: Integrating Redis for Cache & Sessions</h3>
    <p class="text-gray-700 mb-6">To improve performance, we will configure Redis as the driver for cache, sessions, and queues.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.1. Install the Predis Client via Composer</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require predis/predis</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.2. Update the .env File</h4>
            <p class="text-gray-700 mb-4">Modify these variables to make Laravel use Redis. Note: if the line <code>CACHE_STORE=database</code> is present, it must be removed to avoid conflicts.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-ini">CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.3. Check Configuration Files</h4>
            <p class="text-gray-700 mb-4">Ensure that the <code>config/database.php</code> and <code>config/cache.php</code> files are correctly set up to use these Redis environment variables.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-php">
// In config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
],

// In config/cache.php
'stores' => [
    // ...
    'redis' => [
        'driver' => 'redis',
        'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
        'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
    ],
    // ...
],
                </code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.4. Test the Redis Connection</h4>
            <p class="text-gray-700 mb-4">Use the Redis command-line interface to ensure the server is running and responsive.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">redis-cli
127.0.0.1:6379> ping
PONG
127.0.0.1:6379> exit</code></pre>
                <button class="copy-btn">Copy</button>
            </div>
        </div>
    </div>
</section>