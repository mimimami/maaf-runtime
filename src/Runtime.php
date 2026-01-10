<?php

declare(strict_types=1);

namespace MAAF\Runtime;

use MAAF\Core\Container\ContainerInterface;
use MAAF\Core\Http\Kernel;
use MAAF\Runtime\Bootstrap\BootstrapCache;
use MAAF\Runtime\Preload\PreloadManager;
use MAAF\Runtime\Worker\WorkerManager;

/**
 * Runtime
 * 
 * Fő runtime osztály worker alapú futtatáshoz.
 * 
 * @version 1.0.0
 */
final class Runtime
{
    private ?WorkerManager $workerManager = null;
    private ?BootstrapCache $bootstrapCache = null;
    private ?PreloadManager $preloadManager = null;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Kernel $kernel,
        private readonly bool $enableCache = true,
        private readonly bool $enablePreload = true
    ) {
        if ($this->enableCache) {
            $this->bootstrapCache = new BootstrapCache();
        }

        if ($this->enablePreload) {
            $this->preloadManager = new PreloadManager();
        }
    }

    /**
     * Bootstrap application
     * 
     * @param callable $bootstrap Bootstrap callback
     * @return ContainerInterface
     */
    public function bootstrap(callable $bootstrap): ContainerInterface
    {
        if ($this->bootstrapCache !== null && $this->bootstrapCache->isCached() && $this->bootstrapCache->isValid()) {
            return $this->bootstrapCache->load();
        }

        $container = $bootstrap();

        if ($this->bootstrapCache !== null) {
            $this->bootstrapCache->save($container);
        }

        return $container;
    }

    /**
     * Start runtime with workers
     * 
     * @param int $workerCount Worker count
     * @param int $maxRequests Max requests per worker
     * @return void
     */
    public function start(int $workerCount = 4, int $maxRequests = 1000): void
    {
        $this->workerManager = new WorkerManager(
            $this->container,
            $this->kernel,
            $maxRequests
        );
        $this->workerManager->setWorkerCount($workerCount);
        $this->workerManager->start();
    }

    /**
     * Stop runtime
     * 
     * @return void
     */
    public function stop(): void
    {
        if ($this->workerManager !== null) {
            $this->workerManager->stop();
        }
    }

    /**
     * Reload runtime
     * 
     * @return void
     */
    public function reload(): void
    {
        if ($this->workerManager !== null) {
            $this->workerManager->reload();
        }
    }

    /**
     * Register module for preload
     * 
     * @param string $moduleName Module name
     * @param string $modulePath Module path
     * @return void
     */
    public function registerModulePreload(string $moduleName, string $modulePath): void
    {
        if ($this->preloadManager !== null) {
            $this->preloadManager->registerModule($moduleName, $modulePath);
        }
    }

    /**
     * Register file for preload
     * 
     * @param string $filePath File path
     * @return void
     */
    public function registerFilePreload(string $filePath): void
    {
        if ($this->preloadManager !== null) {
            $this->preloadManager->registerFile($filePath);
        }
    }

    /**
     * Generate preload script
     * 
     * @return void
     */
    public function generatePreload(): void
    {
        if ($this->preloadManager !== null) {
            $this->preloadManager->generate();
        }
    }

    /**
     * Clear bootstrap cache
     * 
     * @return void
     */
    public function clearBootstrapCache(): void
    {
        if ($this->bootstrapCache !== null) {
            $this->bootstrapCache->clear();
        }
    }

    /**
     * Clear preload
     * 
     * @return void
     */
    public function clearPreload(): void
    {
        if ($this->preloadManager !== null) {
            $this->preloadManager->clear();
        }
    }

    /**
     * Get worker manager
     * 
     * @return WorkerManager|null
     */
    public function getWorkerManager(): ?WorkerManager
    {
        return $this->workerManager;
    }

    /**
     * Get bootstrap cache
     * 
     * @return BootstrapCache|null
     */
    public function getBootstrapCache(): ?BootstrapCache
    {
        return $this->bootstrapCache;
    }

    /**
     * Get preload manager
     * 
     * @return PreloadManager|null
     */
    public function getPreloadManager(): ?PreloadManager
    {
        return $this->preloadManager;
    }
}
