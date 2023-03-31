<?php

namespace App\Event\Employee;

use App\Entity\Employee;

final class EmployeeCreated
{
    public function __construct(
        public readonly Employee $employee,
    ) {}
}