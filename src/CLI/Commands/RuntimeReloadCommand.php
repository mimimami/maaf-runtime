<?php

declare(strict_types=1);

namespace MAAF\Runtime\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Runtime\Runtime;

/**
 * Runtime Reload Command
 * 
 * Runtime újratöltése.
 * 
 * @version 1.0.0
 */
final class RuntimeReloadCommand implements CommandInterface
{
    public function __construct(
        private readonly ?Runtime $runtime = null
    ) {
    }

    public function getName(): string
    {
        return 'runtime:reload';
    }

    public function getDescription(): string
    {
        return 'Reload runtime workers';
    }

    public function execute(array $args): int
    {
        if ($this->runtime === null) {
            echo "❌ Runtime not available\n";
            return 1;
        }

        echo "Reloading runtime...\n";
        $this->runtime->reload();

        echo "✅ Runtime reloaded\n";
        return 0;
    }
}
