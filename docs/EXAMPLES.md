# MAAF Runtime Példák

## Alapvető Használat

### Runtime Létrehozása

```php
use MAAF\Runtime\Runtime;
use MAAF\Core\Container\Container;
use MAAF\Core\Http\Kernel;

$container = new Container();
$kernel = new Kernel($container);

$runtime = new Runtime(
    container: $container,
    kernel: $kernel,
    enableCache: true,
    enablePreload: true
);
```

### Bootstrap Cache-eléssel

```php
// Bootstrap with cache
$container = $runtime->bootstrap(function() {
    $container = new Container();
    // ... setup container
    return $container;
});

// Next time, cached bootstrap is used
$container = $runtime->bootstrap(function() {
    return new Container();
});
```

## Worker Alapú Futtatás

### Worker Manager

```php
use MAAF\Runtime\Worker\WorkerManager;

$workerManager = new WorkerManager(
    container: $container,
    kernel: $kernel,
    maxRequests: 1000
);

// Set worker count
$workerManager->setWorkerCount(4);

// Start workers
$workerManager->start();

// Workers handle requests automatically
// After 1000 requests, workers reload automatically

// Stop workers
$workerManager->stop();

// Reload workers manually
$workerManager->reload();
```

### HTTP Worker

```php
use MAAF\Runtime\Worker\HttpWorker;

$worker = new HttpWorker(
    container: $container,
    kernel: $kernel,
    maxRequests: 1000
);

$worker->start();

// Handle request
$request = Request::fromGlobals();
$response = $worker->handle($request);

// Check request count
echo "Requests handled: " . $worker->getRequestCount() . "\n";
```

## Bootstrap Cache

### Cache Használat

```php
use MAAF\Runtime\Bootstrap\BootstrapCache;

$cache = new BootstrapCache('storage/cache/bootstrap.php');

// Cache bootstrap
$container = $cache->cache(function() {
    $container = new Container();
    // ... expensive bootstrap operations
    return $container;
});

// Check cache
if ($cache->isCached() && $cache->isValid()) {
    // Use cached bootstrap
    $container = $cache->load();
} else {
    // Rebuild bootstrap
    $container = bootstrap();
    $cache->save($container);
}
```

### Cache Validáció

```php
$cache = new BootstrapCache();

// Check if cache exists
if ($cache->isCached()) {
    // Check if cache is valid (newer than bootstrap files)
    if ($cache->isValid()) {
        $container = $cache->load();
    } else {
        // Cache is stale, rebuild
        $container = bootstrap();
        $cache->save($container);
    }
}
```

## Modul Preload

### Preload Manager

```php
use MAAF\Runtime\Preload\PreloadManager;

$preload = new PreloadManager('storage/preload.php');

// Register core files
$preload->registerFile('vendor/autoload.php');
$preload->registerFile('bootstrap.php');

// Register modules
$preload->registerModule('UserModule', 'src/Modules/UserModule');
$preload->registerModule('ProductModule', 'src/Modules/ProductModule');

// Generate preload script
$preload->generate();

// Get preload path for php.ini
echo "Preload path: " . $preload->getPreloadPath() . "\n";
```

### PHP.ini Konfiguráció

```ini
; Enable OPcache
opcache.enable=1
opcache.enable_cli=1

; Enable preload
opcache.preload=storage/preload.php

; Preload user
opcache.preload_user=www-data
```

### Preload Script Tartalma

```php
<?php

declare(strict_types=1);

// MAAF Runtime Preload Script
// Auto-generated, do not edit manually

opcache_compile_file('vendor/autoload.php');
opcache_compile_file('bootstrap.php');

// Preload module: UserModule
opcache_compile_file('src/Modules/UserModule/Module.php');
opcache_compile_file('src/Modules/UserModule/Controllers/UserController.php');
// ... more files

// Preload module: ProductModule
opcache_compile_file('src/Modules/ProductModule/Module.php');
// ... more files
```

## Teljes Példa

### Server Bootstrap

```php
<?php

declare(strict_types=1);

use MAAF\Runtime\Runtime;
use MAAF\Core\Container\Container;
use MAAF\Core\Http\Kernel;

// Create runtime
$runtime = new Runtime(
    container: new Container(),
    kernel: new Kernel(new Container()),
    enableCache: true,
    enablePreload: true
);

// Bootstrap with cache
$container = $runtime->bootstrap(function() {
    $container = new Container();
    
    // Register services
    // Load modules
    // Setup routes
    
    return $container;
});

// Register modules for preload
$runtime->registerModulePreload('UserModule', 'src/Modules/UserModule');
$runtime->registerModulePreload('ProductModule', 'src/Modules/ProductModule');

// Generate preload script
$runtime->generatePreload();

// Start runtime with 4 workers
$runtime->start(workerCount: 4, maxRequests: 1000);

// Runtime is now handling requests
```

### CLI Használat

```bash
# Start runtime with 4 workers, 1000 max requests
php maaf runtime:start 4 1000

# Stop runtime
php maaf runtime:stop

# Reload workers
php maaf runtime:reload

# Generate preload script
php maaf preload:generate

# Clear bootstrap cache
php maaf cache:clear
```

## Teljesítmény Optimalizálás

### Bootstrap Cache

```php
// Expensive bootstrap operations cached
$container = $runtime->bootstrap(function() {
    $container = new Container();
    
    // Expensive operations
    $container->set('config', loadConfigFiles());
    $container->set('routes', loadRoutes());
    $container->set('modules', loadModules());
    
    return $container;
});

// Subsequent requests use cached bootstrap
// Much faster startup time
```

### Preload Optimalizálás

```php
// Preload frequently used classes
$runtime->registerFilePreload('vendor/maaf/core/src/Http/Kernel.php');
$runtime->registerFilePreload('vendor/maaf/core/src/Routing/Router.php');

// Preload module classes
$runtime->registerModulePreload('UserModule', 'src/Modules/UserModule');

// Generate preload script
$runtime->generatePreload();

// Classes are preloaded in OPcache
// Faster class loading
```

## Worker Kezelés

### Worker Pool

```php
// Start with 4 workers
$runtime->start(workerCount: 4, maxRequests: 1000);

// Each worker handles requests independently
// After 1000 requests, worker reloads automatically
// This prevents memory leaks

// Manual reload
$runtime->reload();

// Stop all workers
$runtime->stop();
```

### Worker Status

```php
$workerManager = $runtime->getWorkerManager();

if ($workerManager !== null) {
    $status = $workerManager->getStatus();
    $count = $workerManager->getWorkerCount();
    
    echo "Status: {$status->value}\n";
    echo "Workers: {$count}\n";
}
```
