<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    /**
     * @param int $page
     * @param int $itemsPerPage
     * @param array $params
     * @return array
     */
    public function findAllByParams(int $page, int $itemsPerPage, array $params = []): array
    {
        $queryBuilder = $this->createQueryBuilder('products');

        if (isset($params['name'])) {
            $queryBuilder
                ->andWhere('products.name LIKE :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        return $queryBuilder
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();
    }

}
