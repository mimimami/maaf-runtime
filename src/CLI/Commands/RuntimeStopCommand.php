<?php

declare(strict_types=1);

namespace MAAF\Runtime\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Runtime\Runtime;

/**
 * Runtime Stop Command
 * 
 * Runtime leállítása.
 * 
 * @version 1.0.0
 */
final class RuntimeStopCommand implements CommandInterface
{
    public function __construct(
        private readonly ?Runtime $runtime = null
    ) {
    }

    public function getName(): string
    {
        return 'runtime:stop';
    }

    public function getDescription(): string
    {
        return 'Stop runtime';
    }

    public function execute(array $args): int
    {
        if ($this->runtime === null) {
            echo "❌ Runtime not available\n";
            return 1;
        }

        echo "Stopping runtime...\n";
        $this->runtime->stop();

        echo "✅ Runtime stopped\n";
        return 0;
    }
}
