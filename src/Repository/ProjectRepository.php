<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
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

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deliver(Project $entity): void
    {
        $entity->setDeliveryDate(new \DateTimeImmutable());

        $this->getEntityManager()->flush();
    }

    /**
     * @return array<Project>
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Project>
     */
    public function findLastProjects(int $size): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pt')
            ->leftJoin('p.productionTimes', 'pt')
            ->addSelect('e')
            ->leftJoin('pt.employee', 'e')
            ->groupBy('p.id')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($size)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Project>
     */
    public function findAllWithProductionTimes(): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pt')
            ->leftJoin('p.productionTimes', 'pt')
            ->addSelect('e')
            ->leftJoin('pt.employee', 'e')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ?Project
     * @throws NonUniqueResultException
     */
    public function findOneById(string $id): ?Project
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
