<?php

namespace App\Manager;

use App\Entity\Job;
use App\Event\Job\JobCreated;
use App\Event\Job\JobDeleted;
use App\Event\Job\JobUpdated;
use App\Repository\JobRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class JobManager
{
    public function __construct(
        private JobRepository $jobRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(Job $job): void
    {
        $this->jobRepository->save($job, true);

        $this->eventDispatcher->dispatch(new JobCreated($job));
    }

    public function update(Job $job): void
    {
        $this->jobRepository->update();

        $this->eventDispatcher->dispatch(new JobUpdated($job));
    }

    public function delete(Job $job): void
    {
        $this->jobRepository->remove($job, true);

        $this->eventDispatcher->dispatch(new JobDeleted($job));
    }
}