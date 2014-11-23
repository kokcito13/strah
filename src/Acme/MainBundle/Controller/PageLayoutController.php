<?php

namespace Acme\MainBundle\Controller;

//use Doctrine\Common\Cache\ApcCache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageLayoutController extends Controller
{
    /**
     * @Template()
     */
    public function navigateAction($main = false)
    {
        return array('entities' => $this->categoryList(), 'main' => $main);
    }
    /**
     * @Template()
     */
    public function footerAction($main = false)
    {
        return array('entities' => $this->categoryList(), 'main' => $main);
    }

    public function categoryList()
    {
//        $cacheDriver = new ApcCache();
//        $result = $cacheDriver->fetch('categories');
//        if (!$result) {
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('AcmeMainBundle:Category')->findAll();

//            $cacheDriver->save('categories', serialize($entities), (3600*24));
//        } else {
//            $entities = unserialize($result);
//        }
//
//        $entities = array();

        return $entities;
    }
}
