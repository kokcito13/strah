<?php

namespace Acme\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Acme\MainBundle\Entity\Post;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    /**
     * @param Tag $tag
     * @return array
     */
    public function findByTag(Tag $tag)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder()
            ->select('p')
            ->from('AcmeMainBundle:Post', 'p')
            ->join('p.tags', 't')
            ->where('t.id = :tag')

            ->setParameter('tag', $tag->getId())

            ->orderBy('p.id', 'DESC')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * @param Post $post
     * @return array
     */
    public function findOther(Post $post)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder()
            ->select('p, c')
            ->from('AcmeMainBundle:Post', 'p')
            ->join('p.category', 'c')
            ->where('c.id = :category')
            ->andWhere('p.id > :post_id')

            ->setParameter('post_id', $post->getId())
            ->setParameter('category', $post->getCategory()->getId())

            ->orderBy('p.id', 'DESC')
            ->setMaxResults(3)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findById($id)
    {

        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select('p, c, t')
            ->from('AcmeMainBundle:Post', 'p')
            ->join('p.category', 'c')
            ->join('p.tags', 't')
            ->where('p.id = :post_id')

            ->setParameter('post_id', $id)

            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        return count($result)?current($result):$result;
    }
    
    /**
     * @param $searchValue
     * @return array
    */
    public function getSearchQuery($searchValue)
    {

        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
           ->select('p, c')
           ->from('AcmeMainBundle:Post', 'p')
           ->join('p.category', 'c')
           ->where('p.name LIKE :searchValue')
           ->orWhere('p.text LIKE :searchValue')

           ->setParameter('searchValue', "%".$searchValue."%")

           ->orderBy('p.id', 'DESC')
        ;

        $result = $query->getQuery();

        return $result;
    }

    public function findForPage($order = false, $limit = false)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder()
            ->select('p')
            ->from('AcmeMainBundle:Post', 'p');

        if (!$order)
            $query->orderBy('p.id', 'DESC');

        if ($limit)
            $query->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }
}
