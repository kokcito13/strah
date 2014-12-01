<?php

namespace Acme\MainBundle\Controller;

use Doctrine\Common\Cache\ApcCache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * @Route("/", name="page_home")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Route("/sitemap.xml", name="sitemap_xml")
     */
    public function viewSitemapXmlAction(Request $request)
    {
//        $cacheKey = 'bohenon_sitemap_xml_test';
//        $cache = new ApcCache();
//        $sitemap = $cache->fetch($cacheKey);
//        if (!$sitemap ) {
            
            $repo = $this->getDoctrine()->getRepository('AcmeMainBundle:Post');
            $allPosts = $repo->findAll();
            
            $categories = $this->getDoctrine()->getRepository('AcmeMainBundle:Category');
            $allCategories = $categories->findAll();
            
            $rootUrl = $request->getSchemeAndHttpHost();
            
            $rootNode = new \SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8'?><urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'></urlset>" );
            
            // adding home page
            $itemNode = $rootNode->addChild('url');
            $itemNode->addChild( 'loc', $rootUrl . $this->generateUrl('page_home') );
            $itemNode->addChild( 'changefreq', 'daily' );
            $itemNode->addChild( 'priority', '1.0' );
            
            // adding all categories
            foreach ( $allCategories as $cat ) {
                $url = $rootUrl . $this->generateUrl('client_category_show', array('category_url' => $cat->getUrl()));

                $itemNode = $rootNode->addChild('url');
                $itemNode->addChild( 'loc', $url );
                $itemNode->addChild( 'changefreq', 'monthly' );
                $itemNode->addChild( 'priority', '0.9' );
            }
            
            // adding all posts
            foreach ( $allPosts as $post ) {
                $url = $rootUrl . $this->generateUrl('client_post_show', array('category_url' => $post->getCategory()->getUrl(), 'post_url' => $post->getUrl() ));

                $itemNode = $rootNode->addChild('url');
                $itemNode->addChild( 'loc', $url );
                $itemNode->addChild( 'lastmod', $post->getCreatedAt()->format('Y-m-d') );
                $itemNode->addChild( 'changefreq', 'monthly' );
                $itemNode->addChild( 'priority', '0.8' );
            }
            $sitemap = $rootNode->asXML();
//            $cache->save($cacheKey, $sitemap, (864000)); // 10 days cache 24*10*60*60
//        }

        return new Response($sitemap);
    }
    
    /**
     * @Route("/posts", name="list_of_post_main")
     */
    public function getItems(Request $request)
    {
        $avalancheService = $this->get('imagine.cache.path.resolver');
        $page = $request->query->get('page', 1);
        $cacheDriver = new ApcCache();
        $result = $cacheDriver->fetch('index_items_page_'.$page);
        if (!$result) {
            $em    = $this->get('doctrine.orm.entity_manager');
            $dql   = "SELECT p FROM AcmeMainBundle:Post p JOIN p.category cat ORDER BY p.id DESC";
            $query = $em->createQuery($dql);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $this->get('request')->query->get('page', $page),
                10
            );

            $pagData = $pagination->getPaginationData();
            $arr = array();
            foreach ($pagination->getItems() as $item) {
                $thumbnail = $item->getImageThumbnail();
                $arr[] = array(
                    'name' => $item->getName(),
                    'img' => $avalancheService->getBrowserPath($item->getWebPath(), $thumbnail['key']),
                    'img_width' => $thumbnail['par']['w'],
                    'img_height' => $thumbnail['par']['h'],
                    'url' => $this->generateUrl('client_post_show', array('category_url' => $item->getCategory()->getUrl(), 'post_url' => $item->getUrl() )),
                    'category_name' => $item->getCategory()->getName(),
                    'date' => $item->getCreatedAtByFormat('d F / Y'),
                    'page' => isset($pagData['next'])?$pagData['next']:$page
                );
            }
            $cacheDriver->save('index_items_page_'.$page, json_encode($arr), (3600*24));
        } else {
            $arr = json_decode($result, true);
        }

        return new JsonResponse($arr);
    }

    /**
     * @Route("/contact", name="page_contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {

        return array();
    }

    /**
     * @Route("/advertising", name="page_advertising")
     * @Template()
     */
    public function advertisingAction(Request $request)
    {

        return array();
    }

    /**
     * @Route("/sitemap", name="page_sitemap")
     * @Template()
     */
    public function sitemapAction(Request $request)
    {

        return array();
    }

    /**
     * @Route("/rule", name="page_rule")
     * @Template()
     */
    public function ruleAction(Request $request)
    {

        return array();
    }

    /**
     * @Route("/catalog", name="page_catalog")
     * @Template()
     */
    public function catalogAction(Request $request)
    {

        return array();
    }
}
