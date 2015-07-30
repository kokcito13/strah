<?php

namespace Acme\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{

    /**
     * @param bool $limit
     * @return array
     */
    public function getLastComments($limit = false)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder()
            ->select('c')
            ->from('AcmeMainBundle:Comment', 'c');

        $query
            ->orderBy('c.id', 'DESC');

        if ($limit) {
            $query->setMaxResults($limit);
        }

        $result = $query->getQuery()->getResult();

        return $result;
    }
}
