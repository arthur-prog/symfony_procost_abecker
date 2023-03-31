<?php

namespace App\Event\Project;

use App\Entity\Project;

final class ProjectDeleted
{
    public function __construct(
        public readonly Project $project,
    ) {}
}