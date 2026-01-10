<?php

declare(strict_types=1);

namespace MAAF\Runtime\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Runtime\Runtime;

/**
 * Cache Clear Command
 * 
 * Bootstrap cache törlése.
 * 
 * @version 1.0.0
 */
final class CacheClearCommand implements CommandInterface
{
    public function __construct(
        private readonly ?Runtime $runtime = null
    ) {
    }

    public function getName(): string
    {
        return 'cache:clear';
    }

    public function getDescription(): string
    {
        return 'Clear bootstrap cache';
    }

    public function execute(array $args): int
    {
        if ($this->runtime === null) {
            echo "❌ Runtime not available\n";
            return 1;
        }

        echo "Clearing bootstrap cache...\n";
        $this->runtime->clearBootstrapCache();

        echo "✅ Bootstrap cache cleared\n";
        return 0;
    }
}
