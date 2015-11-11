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
    public function navigateAction($main = false, $route_name = '', $route_attributes = array())
    {
        return array('entities' => $this->categoryList(),
            'main' => $main,
            'route_name' => $route_name,
            'route_attributes' => $route_attributes);
    }
    /**
     * @Template()
     */
    public function footerAction($main = false, $route_name = '', $post = false, $company = false)
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

        $cId = false;
        if ($company)
            $cId = $company->getId();
        $companies = $em->getRepository('AcmeMainBundle:Company')->getTop($cId, 7);

        return array(
            'entities' => $this->categoryList(),
            'main' => $main,
            'posts' => $entities,
            'route_name' => $route_name,
            'companies' => $companies
        );
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

        $companies = $em->getRepository('AcmeMainBundle:Company')->getTop(false, 3);

        return array('post' => $post, 'entities' => $entities, 'companies' => $companies);
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


    /**
     * @param string $route_name
     * @Template()
     * @return array
     */
    public function footerNewAction($route_name = '')
    {
        return array(
            'route_name' => $route_name,
        );
    }

    /**
     * @param bool|false $main
     * @param string $route_name
     * @param array $route_attributes
     * @Template()
     * @return array
     */
    public function navigateNewAction($main = false, $route_name = '', $route_attributes = array())
    {
        return array('entities' => $this->categoryList(),
            'main' => $main,
            'route_name' => $route_name,
            'route_attributes' => $route_attributes);
    }
}
