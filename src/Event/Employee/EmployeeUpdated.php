<?php

namespace App\Event\Employee;

use App\Entity\Employee;

final class EmployeeUpdated
{
    public function __construct(
        public readonly Employee $employee,
    ) {}
}