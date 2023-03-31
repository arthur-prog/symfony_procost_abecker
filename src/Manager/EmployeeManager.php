<?php

namespace App\Manager;

use App\Entity\Employee;
use App\Event\Employee\EmployeeCreated;
use App\Event\Employee\EmployeeUpdated;
use App\Repository\EmployeeRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EmployeeManager
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(Employee $employee): void
    {
        $this->employeeRepository->save($employee, true);

        $this->eventDispatcher->dispatch(new EmployeeCreated($employee));
    }

    public function update(Employee $employee): void
    {
        $this->employeeRepository->update();

        $this->eventDispatcher->dispatch(new EmployeeUpdated($employee));
    }
}