<!-- =================================================================== -->
<!-- PARTIE 1 : INITIALISATION DU PROJET ET PRÉREQUIS -->
<!-- =================================================================== -->
<h2 class="text-3xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2 mb-6">Partie 1 : Initialisation du Projet et Prérequis</h2>

<!-- ========== BLOC D'INFORMATION GITHUB ========== -->
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-sm mb-12" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 9a.75.75 0 00-1.5 0v2.25H9a.75.75 0 000 1.5h2.25V15a.75.75 0 001.5 0v-2.25H15a.75.75 0 000-1.5h-2.25V9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">
                Ce cours est accompagné d'un projet de démonstration complet et open-source. Vous pouvez consulter, cloner ou contribuer au code source directement sur GitHub.
                <a href="https://www.github.com/ousrah/multitenants" target="_blank" rel="noopener noreferrer" class="font-bold underline hover:text-blue-600">
                    Voir le projet sur GitHub &rarr;
                </a>
            </p>
        </div>
    </div>
</div>

<!-- ========== CHAPITRE 1 : INSTALLATION DE LARAVEL 12 ET DU LAYOUT ========== -->
<section id="install-laravel" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 1 : Installation de Laravel 12 et du Layout de base</h3>
    <p class="text-gray-700 mb-6">Nous commençons par la base : la création d'un projet Laravel 12 frais, la configuration de la base de données et la mise en place d'une structure de layout simple avec un composant Blade pour être prêts à construire notre interface.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.1. Création du projet Laravel</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer create-project laravel/laravel multitenants</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.2. Configuration de l'environnement (.env)</h4>
            <p class="text-gray-700 mb-4">Modifiez votre fichier <code>.env</code> pour connecter le projet à votre base de données MySQL centrale.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-ini">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_multitenant_central
DB_USERNAME=root
DB_PASSWORD=</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.3. Lancement de la première migration</h4>
            <p class="text-gray-700 mb-4">Exécutez la migration initiale pour créer les tables de base de Laravel (utilisateurs, etc.) dans la base de données centrale.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan migrate</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">1.4. Création du composant de Layout</h4>
            <p class="text-gray-700 mb-4">Nous créons un composant <code>AppLayout</code> qui servira de structure principale pour les pages de notre application.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">php artisan make:component AppLayout</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
            <p class="text-gray-700 my-4">Cela génère <code>App/View/Components/AppLayout.php</code> et <code>resources/views/components/app-layout.blade.php</code>. Vous pouvez ensuite structurer votre layout en y incluant des partiels comme un header et une sidebar (<code>main-sidebar.blade.php</code>).</p>
        </div>
    </div>
</section>

<!-- ========== CHAPITRE 2 : INTÉGRATION DE REDIS ========== -->
<section id="config-redis" class="mb-16">
    <h3 class="text-2xl font-semibold mb-3">Chapitre 2 : Intégration de Redis pour le Cache & Sessions</h3>
    <p class="text-gray-700 mb-6">Pour améliorer les performances, nous configurons Redis comme pilote pour le cache, les sessions et les files d'attente.</p>
    
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.1. Installation du client Predis via Composer</h4>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">composer require predis/predis</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.2. Mise à jour du fichier .env</h4>
            <p class="text-gray-700 mb-4">Modifiez ces variables pour que Laravel utilise Redis. Attention, si la ligne <code>CACHE_STORE=database</code> est présente, elle doit être supprimée pour éviter les conflits.</p>
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
                <button class="copy-btn">Copier</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.3. Vérification des fichiers de configuration</h4>
            <p class="text-gray-700 mb-4">Assurez-vous que les fichiers <code>config/database.php</code> et <code>config/cache.php</code> sont bien configurés pour utiliser ces variables d'environnement Redis.</p>
            <details class="mt-4">
                <summary class="cursor-pointer text-blue-600">Voir la configuration type</summary>
                <div class="mt-2">
                    <p class="text-sm font-semibold mb-2">Dans <code>config/database.php</code> :</p>
                    <div class="code-block-wrapper">
<pre class="code-block"><code class="language-php">'redis' => [
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
],</code></pre>
                        <button class="copy-btn">Copier</button>
                    </div>
                    <p class="text-sm font-semibold mt-4 mb-2">Dans <code>config/cache.php</code> :</p>
                    <div class="code-block-wrapper">
<pre class="code-block"><code class="language-php">'stores' => [
    // ...
    'redis' => [
        'driver' => 'redis',
        'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
        'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
    ],
    // ...
],</code></pre>
                        <button class="copy-btn">Copier</button>
                    </div>
                </div>
            </details>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h4 class="text-xl font-bold text-gray-800 mb-2">2.4. Tester la connexion Redis</h4>
            <p class="text-gray-700 mb-4">Utilisez l'interface en ligne de commande de Redis pour vous assurer que le serveur fonctionne et répond.</p>
            <div class="code-block-wrapper">
                <pre class="code-block"><code class="language-bash">redis-cli
127.0.0.1:6379> ping
PONG
127.0.0.1:6379> exit</code></pre>
                <button class="copy-btn">Copier</button>
            </div>
        </div>
    </div>
</section>