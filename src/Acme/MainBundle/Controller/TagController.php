<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\PostRepository;
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
 * @Route("/tag")
 */
class TagController extends Controller
{
    /**
     *
     * @Route("/{tag_url}", name="client_tag_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($tag_url)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcmeMainBundle:Tag')->findOneByUrl($tag_url);
        if (!$entity) {
            throw $this->createNotFoundException('Просим нас извинить но данную страницу тегов мы неможем найти.');
        }
        /** @var PostRepository $postRepo */
        $postRepo = $em->getRepository('AcmeMainBundle:Post');
        $entities = $postRepo->findByTag($entity);

        return array(
            'entity' => $entity,
            'entities' => $entities
        );
    }

}