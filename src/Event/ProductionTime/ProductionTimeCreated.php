<?php

namespace App\Event\ProductionTime;

use App\Entity\ProductionTime;

final class ProductionTimeCreated
{
    public function __construct(
        public readonly ProductionTime $productionTime,
    ) {}
}