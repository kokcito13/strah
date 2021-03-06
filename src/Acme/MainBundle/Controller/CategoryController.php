<?php

namespace Acme\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\MainBundle\Entity\Category;
use Acme\MainBundle\Entity\Post;

/**
 * Post controller.
 *
 * @Route("/")
 */
class CategoryController extends Controller
{
    /**
     *
     * @Route("/{category_url}", name="client_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($category_url, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcmeMainBundle:Category')->findOneByUrl($category_url);
        if (!$entity) {
            throw $this->createNotFoundException('Просим нас извинить но данную страницу категории мы неможем найти.');
        }
        $entities = $em->getRepository('AcmeMainBundle:Post')->findByCategory($entity, array('id' => 'DESC'));

		$query = $em->createQuery('
			SELECT p
			FROM AcmeMainBundle:Post p
			JOIN p.category catagory
			WHERE catagory.id = '.$entity->getId().'
			ORDER BY p.id DESC
		');

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 8);
		$pagination->setUsedRoute('client_category_show');

        return array(
			'entity' => $entity,
			'entities' => $entities,
			'pagination'=>$pagination,
			'pageNum' => $request->query->get('page', false),
			'mainCategory' => $category_url
        );
    }

}