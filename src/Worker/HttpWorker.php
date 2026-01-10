<?php

declare(strict_types=1);

namespace MAAF\Runtime\Worker;

use MAAF\Core\Container\ContainerInterface;
use MAAF\Core\Http\Kernel;
use MAAF\Core\Http\Request;

/**
 * HTTP Worker
 * 
 * HTTP kérések kezelésére szolgáló worker.
 * 
 * @version 1.0.0
 */
final class HttpWorker implements WorkerInterface
{
    private WorkerStatus $status = WorkerStatus::STOPPED;
    private int $requestCount = 0;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Kernel $kernel,
        private readonly int $maxRequests = 1000
    ) {
    }

    /**
     * Start worker
     * 
     * @return void
     */
    public function start(): void
    {
        $this->status = WorkerStatus::STARTING;
        $this->requestCount = 0;
        $this->status = WorkerStatus::RUNNING;
    }

    /**
     * Stop worker
     * 
     * @return void
     */
    public function stop(): void
    {
        $this->status = WorkerStatus::STOPPING;
        $this->status = WorkerStatus::STOPPED;
    }

    /**
     * Reload worker
     * 
     * @return void
     */
    public function reload(): void
    {
        if ($this->requestCount >= $this->maxRequests) {
            $this->stop();
            $this->start();
        }
    }

    /**
     * Get worker status
     * 
     * @return WorkerStatus
     */
    public function getStatus(): WorkerStatus
    {
        return $this->status;
    }

    /**
     * Handle request
     * 
     * @param mixed $request Request
     * @return mixed Response
     */
    public function handle(mixed $request): mixed
    {
        if ($this->status !== WorkerStatus::RUNNING) {
            throw new \RuntimeException('Worker is not running');
        }

        $this->requestCount++;

        // Convert to Request object if needed
        if (!$request instanceof Request) {
            $request = Request::fromGlobals();
        }

        // Handle request through kernel
        $response = $this->kernel->handle($request);

        // Check if reload needed
        if ($this->requestCount >= $this->maxRequests) {
            $this->reload();
        }

        return $response;
    }

    /**
     * Get request count
     * 
     * @return int
     */
    public function getRequestCount(): int
    {
        return $this->requestCount;
    }
}
