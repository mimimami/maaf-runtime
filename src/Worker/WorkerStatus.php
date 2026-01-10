<?php

declare(strict_types=1);

namespace MAAF\Runtime\Worker;

/**
 * Worker Status
 * 
 * Worker státusz enum.
 * 
 * @version 1.0.0
 */
enum WorkerStatus: string
{
    case STOPPED = 'stopped';
    case STARTING = 'starting';
    case RUNNING = 'running';
    case RELOADING = 'reloading';
    case STOPPING = 'stopping';
}
