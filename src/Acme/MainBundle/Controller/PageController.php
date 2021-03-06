<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\City;
use Acme\MainBundle\Entity\Company;
use Acme\MainBundle\Entity\Country;
use Doctrine\Common\Cache\ApcCache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PageController extends Controller
{
    /**
     * @Route("/sitemap.xml", name="sitemap_xml")
     */
    public function viewSitemapXmlAction(Request $request)
    {
        $cacheKey = 'dvestrahovki_sitemap_xml';
        $cache = $this->get('cache.m');
        $sitemap = $cache->fetch($cacheKey);
        if (!$sitemap ) {

            $countries = $this->getDoctrine()->getRepository('AcmeMainBundle:Country')->findAll();

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

            // adding all countries and city and company
            foreach ( $countries as $country ) {/** @var Country $country */
                foreach ($country->getCities() as $city) {/** @var City $city */
                    $url = $rootUrl . $this->generateUrl('page_catalog', array('city_url'=>$city->getUrl() ));

                    $itemNode = $rootNode->addChild('url');
                    $itemNode->addChild( 'loc', $url );
                    $itemNode->addChild( 'changefreq', 'monthly' );
                    $itemNode->addChild( 'priority', '0.9' );
                    foreach ($city->getCompanies() as $company) {
                        $url = $rootUrl . $this->generateUrl('client_company_show',
                                array('city_url'=>$city->getUrl(), 'company_url'=>$company->getUrl() ));

                        $itemNode = $rootNode->addChild('url');
                        $itemNode->addChild( 'loc', $url );
                        $itemNode->addChild( 'changefreq', 'monthly' );
                        $itemNode->addChild( 'priority', '0.8' );

                        $url = $rootUrl . $this->generateUrl('client_company_show_comments',
                                array('city_url'=>$city->getUrl(), 'company_url'=>$company->getUrl() ));
                        $itemNode = $rootNode->addChild('url');
                        $itemNode->addChild( 'loc', $url );
                        $itemNode->addChild( 'changefreq', 'monthly' );
                        $itemNode->addChild( 'priority', '0.6' );
                    }
                }
            }
            $sitemap = $rootNode->asXML();
            $cache->save($cacheKey, $sitemap, (2*24*60*60)); // 2 days cache 24*10*60*60
        }

        $response = new Response($sitemap);
        $response->headers->set('Content-Type', 'xml');

        return $response;
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
        $categories = $this->getDoctrine()->getRepository('AcmeMainBundle:Category')->findAll();
        $countries = $this->getDoctrine()->getRepository('AcmeMainBundle:Country')->findAll();

        return array(
            'categories' => $categories,
            'countries' => $countries
        );
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
     * @Route("/{city_url}", name="page_home", defaults={"city_url" = "moscow"},
	 *	requirements={"city_url":"moscow|sankt-peterburg|novosibirsk|perm|ekaterinburg|omsk|rostov-na-donu|samara|krasnoyarsk|krasnodar|nizhny-novgorod"})
     * @Template()
     */
    public function indexAction($city_url, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcmeMainBundle:Post')->findForPage(false, 4);

        $city = $this->get('city.service')->getCity();
        $companiesPopular = $em->getRepository('AcmeMainBundle:Company')
			->findBy(array('city'=>$city, 'status'=>Company::STATUS_ON), array(), 8);

		$comments = $em->getRepository('AcmeMainBundle:Comment')
            ->getLastComments(5, $city);

        $commentsAll = $em->getRepository('AcmeMainBundle:Comment')
            ->findAll();

        return array(
            'entities' => $entities,
            'companies' => $companiesPopular,
            'comments' => $comments,
            'commentsCount' => count($commentsAll)
        );
    }

    /**
     * Rating of companies
     *
     * @Route("/{city_url}/rating", name="company_rating", defaults={"city_url":"moscow"},
	 *	requirements={"city_url":"moscow|sankt-peterburg|novosibirsk|perm|ekaterinburg|omsk|rostov-na-donu|samara|krasnoyarsk|krasnodar|nizhny-novgorod"})
     * @Method("GET")
     * @Template()
     */
    public function ratingAction($city_url)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository('AcmeMainBundle:City')->findOneBy(array(
            'url' => $city_url
        ));
        if (!$city) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти');
        }

        $companyRepo = $em->getRepository('AcmeMainBundle:Company');
        $entities = $companyRepo->findBy(
            array(
                'city' => $city
            ),
            array('rating'=>'DESC')
        );

        return array(
            'companies'  => $entities,
            'city' => $city
        );
    }

    /**
     * Comments of companies
     *
     * @Route("/{city_url}/comments", name="company_comments", defaults={"city_url":"moscow"},
	 *	requirements={"city_url":"moscow|sankt-peterburg|novosibirsk|perm|ekaterinburg|omsk|rostov-na-donu|samara|krasnoyarsk|krasnodar|nizhny-novgorod"})
     * @Method("GET")
     * @Template()
     */
    public function commentsAction($city_url)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository('AcmeMainBundle:City')->findOneBy(array(
            'url' => $city_url
        ));
        if (!$city) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти');
        }

        $comments = $em->getRepository('AcmeMainBundle:Comment')->getCommentsByCity($city_url);

        return array(
            'comments'  => $comments,
            'city' => $city
        );
    }

    /**
     * @Route("/{city_url}/companies", name="page_catalog", defaults={"city_url" = "moscow"},
	 *	requirements={"city_url":"moscow|sankt-peterburg|novosibirsk|perm|ekaterinburg|omsk|rostov-na-donu|samara|krasnoyarsk|krasnodar|nizhny-novgorod"})
     * @Template()
     */
    public function catalogAction(Request $request)
    {
        $city = $this->get('city.service')->getCity();
        $em = $this->getDoctrine()->getManager();

        $title = 'Каталог страховых компаний города - '.$city->getName();
        $description = 'Информации о всех страховых компаниях  города - '.$city->getName();
        $keywords = 'каталог компании страховые агенты '.mb_strtolower($city->getName(), 'UTF8');

		$companies = $em->getRepository('AcmeMainBundle:Company')->findBy(array(
                'city' => $city,
                'status' => Company::STATUS_ON
            ), array('name' => 'ASC'));

        return array(
            'companies' => $companies,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'entity' => $city
        );
    }
}
