# MAAF Runtime Dokumentáció

## Áttekintés

MAAF Runtime egy worker alapú futtatási környezet cache-elt bootstrappal és modul szintű preload-dal.

## Funkciók

- ✅ **Worker Alapú Futtatás** - Octane szerű worker rendszer
- ✅ **Cache-elt Bootstrap** - Bootstrap cache-elés teljesítmény javításhoz
- ✅ **Modul Szintű Preload** - Modulok preload-ja OPcache-ben
- ✅ **Runtime Környezet** - Runtime környezet kezelés
- ✅ **CLI Támogatás** - Runtime kezelés CLI parancsokkal

## Telepítés

```bash
composer require maaf/runtime
```

## Használat

### Alapvető Használat

```php
use MAAF\Runtime\Runtime;
use MAAF\Core\Container\Container;
use MAAF\Core\Http\Kernel;

// Create runtime
$runtime = new Runtime(
    container: $container,
    kernel: $kernel,
    enableCache: true,
    enablePreload: true
);

// Bootstrap with cache
$container = $runtime->bootstrap(function() {
    return new Container();
});

// Start runtime with workers
$runtime->start(workerCount: 4, maxRequests: 1000);
```

### Worker Alapú Futtatás

```php
use MAAF\Runtime\Worker\WorkerManager;

$workerManager = new WorkerManager($container, $kernel, maxRequests: 1000);
$workerManager->setWorkerCount(4);
$workerManager->start();

// Workers handle requests automatically
// After maxRequests, workers reload automatically
```

### Bootstrap Cache

```php
use MAAF\Runtime\Bootstrap\BootstrapCache;

$cache = new BootstrapCache('storage/cache/bootstrap.php');

// Cache bootstrap
$container = $cache->cache(function() {
    return bootstrap();
});

// Check if cached
if ($cache->isCached() && $cache->isValid()) {
    $container = $cache->load();
}

// Clear cache
$cache->clear();
```

### Modul Preload

```php
use MAAF\Runtime\Preload\PreloadManager;

$preload = new PreloadManager('storage/preload.php');

// Register module for preload
$preload->registerModule('UserModule', 'src/Modules/UserModule');

// Register file for preload
$preload->registerFile('vendor/autoload.php');

// Generate preload script
$preload->generate();

// Add to php.ini:
// opcache.preload=storage/preload.php
```

## CLI Parancsok

```bash
# Start runtime
php maaf runtime:start [worker-count] [max-requests]

# Stop runtime
php maaf runtime:stop

# Reload runtime
php maaf runtime:reload

# Generate preload script
php maaf preload:generate

# Clear bootstrap cache
php maaf cache:clear
```

## További információk

- [API Dokumentáció](api.md)
- [Példák](examples.md)
- [Best Practices](best-practices.md)
