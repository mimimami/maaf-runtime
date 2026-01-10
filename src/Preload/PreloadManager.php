<?php

declare(strict_types=1);

namespace MAAF\Runtime\Preload;

/**
 * Preload Manager
 * 
 * Modul szintű preload kezelés OPcache-ben.
 * 
 * @version 1.0.0
 */
final class PreloadManager
{
    /**
     * @var array<string>
     */
    private array $preloadFiles = [];

    /**
     * @var array<string>
     */
    private array $modulePreloads = [];

    private readonly string $preloadPath;

    public function __construct(
        string $preloadPath = 'storage/preload.php'
    ) {
        $this->preloadPath = $preloadPath;
    }

    /**
     * Register file for preload
     * 
     * @param string $filePath File path
     * @return void
     */
    public function registerFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Preload file not found: {$filePath}");
        }

        $this->preloadFiles[] = $filePath;
    }

    /**
     * Register module for preload
     * 
     * @param string $moduleName Module name
     * @param string $modulePath Module path
     * @return void
     */
    public function registerModule(string $moduleName, string $modulePath): void
    {
        if (!is_dir($modulePath)) {
            throw new \RuntimeException("Module path not found: {$modulePath}");
        }

        $this->modulePreloads[$moduleName] = $modulePath;
    }

    /**
     * Generate preload script
     * 
     * @return void
     */
    public function generate(): void
    {
        $preloadDir = dirname($this->preloadPath);
        if (!is_dir($preloadDir)) {
            mkdir($preloadDir, 0755, true);
        }

        $content = "<?php\n\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "// MAAF Runtime Preload Script\n";
        $content .= "// Auto-generated, do not edit manually\n\n";

        // Preload core files
        foreach ($this->preloadFiles as $file) {
            $filePath = addslashes($file);
            $content .= "opcache_compile_file('{$filePath}');\n";
        }

        // Preload module files
        foreach ($this->modulePreloads as $moduleName => $modulePath) {
            $content .= "\n// Preload module: {$moduleName}\n";
            $files = $this->getModuleFiles($modulePath);
            foreach ($files as $file) {
                $filePath = addslashes($file);
                $content .= "opcache_compile_file('{$filePath}');\n";
            }
        }

        file_put_contents($this->preloadPath, $content);
    }

    /**
     * Get module files for preload
     * 
     * @param string $modulePath Module path
     * @return array<int, string>
     */
    private function getModuleFiles(string $modulePath): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }

    /**
     * Get preload path
     * 
     * @return string
     */
    public function getPreloadPath(): string
    {
        return $this->preloadPath;
    }

    /**
     * Clear preload
     * 
     * @return void
     */
    public function clear(): void
    {
        if (file_exists($this->preloadPath)) {
            unlink($this->preloadPath);
        }
    }

    /**
     * Get preload script content
     * 
     * @return string
     */
    public function getPreloadScript(): string
    {
        if (!file_exists($this->preloadPath)) {
            $this->generate();
        }

        return file_get_contents($this->preloadPath);
    }
}
