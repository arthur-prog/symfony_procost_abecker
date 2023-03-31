<?php

namespace App\Event\Job;


use App\Entity\Job;

final class JobUpdated
{
    public function __construct(
        public readonly Job $job,
    ) {}
}