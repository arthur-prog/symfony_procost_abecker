<?php

namespace App\Event\Job;

use App\Entity\Job;

final class JobCreated
{
    public function __construct(
        public readonly Job $job,
    ) {}
}