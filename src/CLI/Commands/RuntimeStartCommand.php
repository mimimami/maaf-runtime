<?php

declare(strict_types=1);

namespace MAAF\Runtime\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Runtime\Runtime;

/**
 * Runtime Start Command
 * 
 * Runtime indítása worker-ekkel.
 * 
 * @version 1.0.0
 */
final class RuntimeStartCommand implements CommandInterface
{
    public function __construct(
        private readonly ?Runtime $runtime = null
    ) {
    }

    public function getName(): string
    {
        return 'runtime:start';
    }

    public function getDescription(): string
    {
        return 'Start runtime with workers';
    }

    public function execute(array $args): int
    {
        if ($this->runtime === null) {
            echo "❌ Runtime not available\n";
            return 1;
        }

        $workerCount = isset($args[0]) ? (int)$args[0] : 4;
        $maxRequests = isset($args[1]) ? (int)$args[1] : 1000;

        echo "Starting runtime with {$workerCount} workers...\n";
        $this->runtime->start($workerCount, $maxRequests);

        echo "✅ Runtime started\n";
        return 0;
    }
}
