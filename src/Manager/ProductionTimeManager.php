<?php

namespace App\Manager;

use App\Entity\ProductionTime;
use App\Event\ProductionTime\ProductionTimeCreated;
use App\Repository\ProductionTimeRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ProductionTimeManager
{
    public function __construct(
        private ProductionTimeRepository $productionTimeRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(ProductionTime $productionTime): void
    {
        $this->productionTimeRepository->save($productionTime, true);

        $this->eventDispatcher->dispatch(new ProductionTimeCreated($productionTime));
    }
}