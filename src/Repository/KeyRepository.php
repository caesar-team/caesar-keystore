<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Key;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Key|null find($id, $lockMode = null, $lockVersion = null)
 * @method Key|null findOneBy(array $criteria, array $orderBy = null)
 * @method Key[]    findAll()
 * @method Key[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyRepository extends ServiceEntityRepository implements KeyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Key::class);
    }

    public function save(Key $key): void
    {
        $this->_em->persist($key);
        $this->_em->flush();
    }

    public function getKeyByEmail(string $email): ?Key
    {
        $queryBuilder = $this
            ->createQueryBuilder('key')
            ->where('LOWER(key.email) = :email')
            ->setParameter('email', mb_strtolower($email))
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getKeysByEmails(array $emails): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('key')
            ->where('LOWER(key.email) IN (:email)')
            ->setParameter('email', array_map('mb_strtolower', $emails))
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
