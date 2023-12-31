<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        // $dql = "SELECT category from App\Entity\Category as category 
        //     ORDER BY category.name ASC";
        //$query = $this->getEntityManager()->createQuery($dql);


        $qb = $this->createQueryBuilder(Category::ALIAS)
            // ->addSelect(FortuneCookie::ALIAS)
            // ->leftJoin(Category::ALIAS . '.fortuneCookies', FortuneCookie::ALIAS)
            ->addOrderBy(Category::ALIAS . '.name', "ASC");
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return Category[]
     */
    public function findBySearch(string $term): array
    {
        $qb = $this->createQueryBuilder(Category::ALIAS)
            ->leftJoin(Category::ALIAS . ".fortuneCookies", FortuneCookie::ALIAS)
            ->addSelect(FortuneCookie::ALIAS)
            ->andWhere(Category::ALIAS . '.name LIKE :searchTerm'
                . ' OR ' . Category::ALIAS . '.iconKey LIKE :searchTerm'
                . ' OR ' . FortuneCookie::ALIAS . '.fortune LIKE :searchTerm')
            ->setParameters(['searchTerm' => "%{$term}%"])
            ->addOrderBy(Category::ALIAS . '.name', 'ASC')
            ->getQuery();
        return $qb->getResult();
    }

    public function getCategoryWithFortunes(int $id): ?Category
    {
        $qb = $this->createQueryBuilder(Category::ALIAS)
            ->addSelect(FortuneCookie::ALIAS)
            ->leftJoin(Category::ALIAS . '.fortuneCookies', FortuneCookie::ALIAS)
            ->andWhere(Category::ALIAS . '.id = :id')
            ->setParameter('id', $id);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
