<?php

namespace App\Manager;

use App\Entity\Project;
use App\Event\Project\ProjectCreated;
use App\Event\Project\ProjectDeleted;
use App\Event\Project\ProjectDelivered;
use App\Event\Project\ProjectUpdated;
use App\Repository\ProjectRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ProjectManager
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(Project $project): void
    {
        $this->projectRepository->save($project, true);

        $this->eventDispatcher->dispatch(new ProjectCreated($project));
    }

    public function update(Project $project): void
    {
        $this->projectRepository->update();

        $this->eventDispatcher->dispatch(new ProjectUpdated($project));
    }

    public function delete(Project $project): void
    {
        $this->projectRepository->remove($project, true);

        $this->eventDispatcher->dispatch(new ProjectDeleted($project));
    }

    public function deliver(Project $project): void
    {
        $this->projectRepository->deliver($project);

        $this->eventDispatcher->dispatch(new ProjectDelivered($project));
    }
}