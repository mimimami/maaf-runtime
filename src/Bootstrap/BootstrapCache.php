<?php

declare(strict_types=1);

namespace MAAF\Runtime\Bootstrap;

use MAAF\Core\Container\ContainerInterface;

/**
 * Bootstrap Cache
 * 
 * Bootstrap cache-elés teljesítmény javításhoz.
 * 
 * @version 1.0.0
 */
final class BootstrapCache
{
    private readonly string $cachePath;

    public function __construct(
        string $cachePath = 'storage/cache/bootstrap.php'
    ) {
        $this->cachePath = $cachePath;
    }

    /**
     * Cache bootstrap
     * 
     * @param callable $bootstrap Bootstrap callback
     * @return ContainerInterface
     */
    public function cache(callable $bootstrap): ContainerInterface
    {
        if ($this->isCached() && $this->isValid()) {
            return $this->load();
        }

        $container = $bootstrap();
        $this->save($container);

        return $container;
    }

    /**
     * Load cached bootstrap
     * 
     * @return ContainerInterface
     */
    public function load(): ContainerInterface
    {
        if (!file_exists($this->cachePath)) {
            throw new \RuntimeException('Bootstrap cache not found');
        }

        return require $this->cachePath;
    }

    /**
     * Save bootstrap to cache
     * 
     * @param ContainerInterface $container Container instance
     * @return void
     */
    public function save(ContainerInterface $container): void
    {
        $cacheDir = dirname($this->cachePath);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Note: In a real implementation, we'd need to serialize the container
        // For now, we'll create a cache file that rebuilds the container
        $cacheContent = <<<'PHP'
<?php

declare(strict_types=1);

// Bootstrap cache - regenerated on cache clear
// This file is auto-generated, do not edit manually

return require __DIR__ . '/../../bootstrap.php';
PHP;

        file_put_contents($this->cachePath, $cacheContent);
    }

    /**
     * Check if bootstrap is cached
     * 
     * @return bool
     */
    public function isCached(): bool
    {
        return file_exists($this->cachePath);
    }

    /**
     * Check if cache is valid
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->isCached()) {
            return false;
        }

        // Check if cache file is newer than bootstrap files
        $cacheTime = filemtime($this->cachePath);
        $bootstrapFile = 'bootstrap.php';

        if (file_exists($bootstrapFile) && filemtime($bootstrapFile) > $cacheTime) {
            return false;
        }

        return true;
    }

    /**
     * Clear bootstrap cache
     * 
     * @return void
     */
    public function clear(): void
    {
        if (file_exists($this->cachePath)) {
            unlink($this->cachePath);
        }
    }

    /**
     * Get cache path
     * 
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }
}
