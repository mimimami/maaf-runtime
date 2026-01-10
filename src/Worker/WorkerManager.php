<?php

declare(strict_types=1);

namespace MAAF\Runtime\Worker;

use MAAF\Core\Container\ContainerInterface;
use MAAF\Core\Http\Kernel;

/**
 * Worker Manager
 * 
 * Worker kezelő Octane szerű futtatáshoz.
 * 
 * @version 1.0.0
 */
final class WorkerManager
{
    /**
     * @var array<int, WorkerInterface>
     */
    private array $workers = [];

    /**
     * @var int
     */
    private int $workerCount = 4;

    /**
     * @var WorkerStatus
     */
    private WorkerStatus $status = WorkerStatus::STOPPED;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Kernel $kernel,
        private readonly int $maxRequests = 1000
    ) {
    }

    /**
     * Set worker count
     * 
     * @param int $count Worker count
     * @return void
     */
    public function setWorkerCount(int $count): void
    {
        $this->workerCount = $count;
    }

    /**
     * Start workers
     * 
     * @return void
     */
    public function start(): void
    {
        if ($this->status !== WorkerStatus::STOPPED) {
            throw new \RuntimeException('Workers are already running');
        }

        $this->status = WorkerStatus::STARTING;

        for ($i = 0; $i < $this->workerCount; $i++) {
            $worker = new HttpWorker(
                $this->container,
                $this->kernel,
                $this->maxRequests
            );
            $worker->start();
            $this->workers[] = $worker;
        }

        $this->status = WorkerStatus::RUNNING;
    }

    /**
     * Stop workers
     * 
     * @return void
     */
    public function stop(): void
    {
        if ($this->status === WorkerStatus::STOPPED) {
            return;
        }

        $this->status = WorkerStatus::STOPPING;

        foreach ($this->workers as $worker) {
            $worker->stop();
        }

        $this->workers = [];
        $this->status = WorkerStatus::STOPPED;
    }

    /**
     * Reload workers
     * 
     * @return void
     */
    public function reload(): void
    {
        if ($this->status !== WorkerStatus::RUNNING) {
            throw new \RuntimeException('Workers are not running');
        }

        $this->status = WorkerStatus::RELOADING;

        foreach ($this->workers as $worker) {
            $worker->reload();
        }

        $this->status = WorkerStatus::RUNNING;
    }

    /**
     * Get status
     * 
     * @return WorkerStatus
     */
    public function getStatus(): WorkerStatus
    {
        return $this->status;
    }

    /**
     * Get worker count
     * 
     * @return int
     */
    public function getWorkerCount(): int
    {
        return count($this->workers);
    }
}
