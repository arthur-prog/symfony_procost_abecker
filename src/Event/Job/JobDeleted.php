<?php

namespace App\Event\Job;

use App\Entity\Job;

final class JobDeleted
{
    public function __construct(
        public readonly Job $job,
    ) {}
}