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
    public function navigateAction($main = false, $route_name = '')
    {
        return array('entities' => $this->categoryList(), 'main' => $main, 'route_name' => $route_name);
    }
    /**
     * @Template()
     */
    public function footerAction($main = false, $route_name = '', $post = false)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AcmeMainBundle:Post')->findForPage(false, 12);
        if ($post) {
            foreach ($entities as $k=>$ent) {
                if ($ent->getId() == $post->getId()) {
                    unset($entities[$k]);
                }
            }
        }

        return array('entities' => $this->categoryList(), 'main' => $main, 'posts' => $entities, 'route_name' => $route_name);
    }

    /**
     * @Template()
     */
    public function rightSidebarAction($post = false)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AcmeMainBundle:Post')->findForPage(false, 5);
        if ($post) {
            foreach ($entities as $k=>$ent) {
                if ($ent->getId() == $post->getId()) {
                    unset($entities[$k]);
                }
            }
        }

        return array('post' => $post, 'entities' => $entities);
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
