<?php

declare(strict_types=1);

namespace MAAF\Runtime\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Runtime\Runtime;

/**
 * Preload Generate Command
 * 
 * Preload script generálása.
 * 
 * @version 1.0.0
 */
final class PreloadGenerateCommand implements CommandInterface
{
    public function __construct(
        private readonly ?Runtime $runtime = null
    ) {
    }

    public function getName(): string
    {
        return 'preload:generate';
    }

    public function getDescription(): string
    {
        return 'Generate preload script';
    }

    public function execute(array $args): int
    {
        if ($this->runtime === null) {
            echo "❌ Runtime not available\n";
            return 1;
        }

        echo "Generating preload script...\n";
        $this->runtime->generatePreload();

        $preloadManager = $this->runtime->getPreloadManager();
        if ($preloadManager !== null) {
            $path = $preloadManager->getPreloadPath();
            echo "✅ Preload script generated: {$path}\n";
            echo "\nAdd to php.ini:\n";
            echo "opcache.preload={$path}\n";
        } else {
            echo "✅ Preload script generated\n";
        }

        return 0;
    }
}
