<?php

namespace App\Repository;

use App\Entity\PostUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostUser>
 *
 * @method PostUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostUser[]    findAll()
 * @method PostUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostUser::class);
    }

    public  function clearUsers() {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeStatement('delete from post_user');
    }

//    /**
//     * @return PostUser[] Returns an array of PostUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PostUser
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
