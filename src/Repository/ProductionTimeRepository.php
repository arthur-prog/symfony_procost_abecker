<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\ProductionTime;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductionTime>
 *
 * @method ProductionTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductionTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductionTime[]    findAll()
 * @method ProductionTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionTime::class);
    }

    public function save(ProductionTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductionTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<ProductionTime>
     */
    public function findLastProductionTimes(int $size): array
    {
        return $this->createQueryBuilder('pt')
            ->addSelect('p')
            ->leftJoin('pt.project', 'p')
            ->addSelect('e')
            ->leftJoin('pt.employee', 'e')
            ->groupBy('pt.id')
            ->orderBy('pt.createdAt', 'DESC')
            ->setMaxResults($size)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<ProductionTime>
     */
    public function findByEmployee(Employee $employee): array
    {
        return $this->createQueryBuilder('pt')
            ->addSelect('p')
            ->leftJoin('pt.project', 'p')
            ->where('pt.employee = :id')
            ->setParameter('id', $employee->getId())
            ->orderBy('pt.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<ProductionTime>
     */
    public function findByProject(Project $project): array
    {
        return $this->createQueryBuilder('pt')
            ->addSelect('e')
            ->leftJoin('pt.employee', 'e')
            ->where('pt.project = :id')
            ->setParameter('id', $project->getId())
            ->orderBy('pt.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
