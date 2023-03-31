<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\Employee\EmployeeCreated;
use App\Event\Employee\EmployeeUpdated;
use App\Event\Job\JobCreated;
use App\Event\Job\JobDeleted;
use App\Event\Job\JobUpdated;
use App\Event\ProductionTime\ProductionTimeCreated;
use App\Event\Project\ProjectCreated;
use App\Event\Project\ProjectDeleted;
use App\Event\Project\ProjectDelivered;
use App\Event\Project\ProjectUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class FlashSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            EmployeeCreated::class =>[
                ['onEmployeeCreated']
            ],
            EmployeeUpdated::class =>[
                ['onEmployeeUpdated']
            ],
            ProductionTimeCreated::class =>[
                ['onProductionTimeCreated']
            ],
            JobCreated::class =>[
                ['onJobCreated']
            ],
            JobUpdated::class =>[
                ['onJobUpdated']
            ],
            JobDeleted::class =>[
                ['onJobDeleted']
            ],
            ProjectCreated::class =>[
                ['onProjectCreated']
            ],
            ProjectUpdated::class =>[
                ['onProjectUpdated']
            ],
            ProjectDeleted::class =>[
                ['onProjectDeleted']
            ],
            ProjectDelivered::class =>[
                ['onProjectDelivered']
            ]
        ];
    }

    public function onEmployeeCreated(EmployeeCreated $event): void
    {
        $this->addFlash('success', 'L\'employé a bien été créé.');
    }

    public function onEmployeeUpdated(EmployeeUpdated $event): void
    {
        $this->addFlash('success', 'L\'employé a bien été modifié.');
    }

    public function onProductionTimeCreated(ProductionTimeCreated $event): void
    {
        $this->addFlash('success', 'Le temps a bien été ajouté.');
    }

    public function onJobCreated(JobCreated $event): void
    {
        $this->addFlash('success', 'Le métier a bien été créé.');
    }

    public function onJobUpdated(JobUpdated $event): void
    {
        $this->addFlash('success', 'Le métier a bien été modifié.');
    }

    public function onJobDeleted(JobDeleted $event): void
    {
        $this->addFlash('success', 'Le métier a bien été supprimé.');
    }

    public function onProjectCreated(ProjectCreated $event): void
    {
        $this->addFlash('success', 'Le projet a bien été créé.');
    }

    public function onProjectUpdated(ProjectUpdated $event): void
    {
        $this->addFlash('success', 'Le projet a bien été modifié.');
    }

    public function onProjectDeleted(ProjectDeleted $event): void
    {
        $this->addFlash('success', 'Le projet a bien été supprimé.');
    }

    public function onProjectDelivered(ProjectDelivered $event): void
    {
        $this->addFlash('success', 'Le projet a bien été marqué comme livré.');
    }

    private function addFlash(string $type, string $message): void
    {
        $session =$this->requestStack->getSession();

        $session->getFlashBag()->add($type, $message);
    }
}
