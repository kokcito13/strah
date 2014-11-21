<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\MainBundle\Entity\Category;
use Acme\MainBundle\Entity\Post;

/**
 * Post controller.
 *
 * @Route("/search")
 */
class SearchController extends Controller
{
    
    /**
     *
     * @Route("/", name="client_search_index")
     * @Template("AcmeMainBundle:Search:show.html.twig")
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $searchValue = $request->request->get('searchValue', false);

        return array(
            'word' => $searchValue
        );
    }
    
    
    /**
     *
     * @Route("/get/", name="client_search_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request)
    {
        
        $resp = array();
        if ($request->isXmlHttpRequest()) {
            $searchValue = $request->query->get('searchValue');

            $avalancheService = $this->get('imagine.cache.path.resolver');
            $page = $request->query->get('page', 1);

            $em = $this->get('doctrine.orm.entity_manager');

            /** @var PostRepository $entityRep */
            $entityRep = $em->getRepository('AcmeMainBundle:Post');
            $query = $entityRep->getSearchQuery($searchValue);


            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $this->get('request')->query->get('page', $page),
                10
            );

            $pagData = $pagination->getPaginationData();
            $arr = array();
            foreach ($pagination->getItems() as $item) { /** @var Post $item */
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

            $resp = $arr;
        } else {
            $resp['error'] = 'not ajax';
        }

        return new JsonResponse($resp);
    }
}