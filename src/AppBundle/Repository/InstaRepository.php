<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;
use PDO;

/**
 * InstaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstaRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllChirps()
    {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM ChirperBundle:Chirp c
                        ORDER BY c.dateAdded DESC'
            );

        return $query->getResult();
    }

    public function getAllInstaByUserId($userId)
    {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Insta c
                        WHERE c.authorId = ?1
                        ORDER BY c.dateAdded DESC'
            );

        $query->setParameter(1, $userId);
        return $query->getResult();
    }

    /**
     * @param $userId
     * @return integer
     */
    public function countAllUserChirps($userId)
    {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT COUNT(c.id) FROM ChirperBundle:Chirp c
                        WHERE c.authorId = ?1'
            );

        $query->setParameter(1, $userId);
        $result = $query->getResult();
        return $result[0][1];
    }

    public function getAllInstaByFollowedUsers($currentUserId)
    {
        $sql = 'SELECT c.*, u2.username
                FROM
                 users AS u
                 INNER JOIN followers AS f ON f.follower_id = u.id
                 INNER JOIN users as u2 ON u2.id = f.followed_id
                 INNER JOIN instabam AS c ON c.authorId = u2.id
                WHERE
                 u.id = :currentUserId
                ORDER BY c.dateAdded DESC';

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);
        $stmt->bindValue('currentUserId', $currentUserId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}