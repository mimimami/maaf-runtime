<?php

declare(strict_types=1);

namespace MAAF\Runtime\Worker;

/**
 * Worker Interface
 * 
 * Worker interface worker alapú futtatáshoz.
 * 
 * @version 1.0.0
 */
interface WorkerInterface
{
    /**
     * Start worker
     * 
     * @return void
     */
    public function start(): void;

    /**
     * Stop worker
     * 
     * @return void
     */
    public function stop(): void;

    /**
     * Reload worker
     * 
     * @return void
     */
    public function reload(): void;

    /**
     * Get worker status
     * 
     * @return WorkerStatus
     */
    public function getStatus(): WorkerStatus;

    /**
     * Handle request
     * 
     * @param mixed $request Request
     * @return mixed Response
     */
    public function handle(mixed $request): mixed;
}
