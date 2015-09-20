<?php
namespace Acme\MainBundle\Service;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityService
{
    protected $currentCity = null;
    protected $em;
    protected $session;

    protected $cityList = array();

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function setCityByUrl($url)
    {
        $city = $this->getCityByUrl($url);
//        if (!$city)
//            throw new NotFoundHttpException('Данную страницу нам неудалось найти!');

        $this->updateSessionCity();
    }

    public function updateSessionCity()
    {
        $cityUrlSession = $this->session->get('city_url', false);
        if ($this->currentCity) {
            $this->session->set('city_url', $this->currentCity->getUrl());
        } elseif ($cityUrlSession) {
            $this->getCityByUrl($cityUrlSession);
        }
    }

    public function getCityByUrl($url)
    {
        return $this->currentCity = $this->em->getRepository('AcmeMainBundle:City')
            ->findOneBy(array('url'=>$url));
    }

    public function getCity()
    {
        if (!$this->currentCity) {
            $this->updateSessionCity();
        }

        if (!$this->currentCity) {
            $this->setCityByUrl('moscow');
        }

        return $this->currentCity;
    }

    public function getCityList()
    {
        if (empty($this->cityList)) {
            $this->cityList = $this->em->getRepository('AcmeMainBundle:City')
                ->findAll();
        }

        return $this->cityList;
    }
}