<?php

namespace App\Event\Project;

use App\Entity\Project;

final class ProjectCreated
{
    public function __construct(
        public readonly Project $project,
    ) {}
}