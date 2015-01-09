<?php
namespace Acme\MainBundle\Service;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Tests\EventListener\TestKernelThatThrowsException;

class CacheM
{

    protected $em;
    protected $status;

    public function __construct(EntityManager $em, $status = true)
    {
        $this->em = $em;
        $this->status = $status;
    }

    public function fetch($key)
    {
        $current = false;
        $query = $this->em->createQueryBuilder()
            ->select('c')
            ->from('AcmeMainBundle:CacheM', 'c')
            ->where("c.keyName = '".$key."'")
            ->setMaxResults(1)
            ->getQuery();
        $caches   = $query->getResult();
        if (count($caches)) {
            $current = current($caches);

            if ($current && !is_null($current->getCreatedAt())) {
                $d = new \DateTime('now');
                $timeStamp = $d->getTimestamp();
                $time = $current->getCreatedAt()->getTimestamp();
                if ($time < $timeStamp) {
                    $current = false;
                }
            } else {
                $current = false;
            }
        }
        $value = '';
        if ($current) {
            $value = $current->getValue();
        }

        return $value;
    }

    public function save($key, $value = '', $date = false)
    {
        if (is_integer($value)) {
            $value = (string)$value;
        }

        if (!is_string($value)) {
            throw new NotFoundHttpException('Value is not string in CacheM');
        }

        $m = new \Acme\MainBundle\Entity\CacheM();
        $query = $this->em->createQueryBuilder()
            ->select('c')
            ->from('AcmeMainBundle:CacheM', 'c')
            ->where("c.keyName = '".$key."'")
            ->setMaxResults(1)
            ->getQuery();
        $caches   = $query->getResult();

        if (count($caches)) {
            $m = current($caches);
        }
        $m->setKeyName($key);
        $m->setValue($value);

        $m->setCreatedAt(NULL);
        if ($date) {
            $d = new \DateTime('now');
            $d->setTimestamp(time()+$date);
            $m->setCreatedAt($d);
        }

        $this->em->persist($m);

        if ($this->status)
            $this->em->flush();
    }

    public function delete($key)
    {
        $query = $this->em->createQueryBuilder()
            ->select('c')
            ->from('AcmeMainBundle:CacheM', 'c')
            ->where("c.keyName = '" . $key . "'")
            ->setMaxResults(1)
            ->getQuery();
        $caches = $query->getResult();

        if (count($caches)) {
            $m = current($caches);
            $this->em->remove($m);
            $this->em->flush();
        }
    }
}