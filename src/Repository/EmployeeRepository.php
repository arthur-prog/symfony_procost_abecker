<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function save(Employee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Employee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Employee>
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('j')
            ->leftJoin('e.job', 'j')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Employee>
     */
    public function findAllWithProductionTimes(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('j')
            ->leftJoin('e.job', 'j')
            ->addSelect('pt')
            ->leftJoin('e.productionTimes', 'pt')
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ?Employee
     * @throws NonUniqueResultException
     */
    public function findOneById(string $id): ?Employee
    {
        return $this->createQueryBuilder('e')
            ->addSelect('j')
            ->leftJoin('e.job', 'j')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
