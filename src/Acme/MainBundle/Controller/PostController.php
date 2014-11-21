<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\MainBundle\Entity\Post;
use Acme\MainBundle\Form\PostType;

/**
 * Post controller.
 *
 * @Route("/")
 */
class PostController extends Controller
{
    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{category_url}/{post_url}", name="client_post_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($category_url, $post_url)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AcmeMainBundle:Category')->findOneByUrl($category_url);
        if (!$category) {
            throw $this->createNotFoundException('Данную статью в этой категории мы неможем найти!');
        }

        /** @var PostRepository $postRepo */
        $postRepo = $em->getRepository('AcmeMainBundle:Post');
        $entity = $postRepo->findOneBy(
            array(
                'category' => $category,
                'url' => $post_url
            )
        );

        $entities = $postRepo->findOther($entity);
        
        if (!$entity) {
            throw $this->createNotFoundException('Данную статью мы неможем найти!');
        }

        $entity->oneMoreView();
        $em->persist($entity);
        $em->flush();

        $author = $entity->getUser();

        return array(
            'entity'        => $entity,
            'entities'      => $entities,
            'author'          => $author
        );
    }

    /**
     * add one share
     *
     * @Route("/social/share", name="add_one_social_for_post")
     * @Method("POST")
     * @Template()
     */
    public function addOneSharedAction(Request $request)
    {
        $arr = array();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $data = (object)$request->request->all();
            /** @var Post $entity */
            $entity = $em->getRepository('AcmeMainBundle:Post')->find($data->id);

            switch ($data->social) {
                case 'fb':
                    $entity->setSharedFb($entity->getSharedFb()+1);
                    break;
                case 'vk':
                    $entity->setSharedVk($entity->getSharedVk()+1);
                    break;
                case 'tw':
                    $entity->setSharedTw($entity->getSharedTw()+1);
                    break;
            }

            $em->persist($entity);
            $em->flush();

            $arr['done'] = true;
        } else {
            $arr['error'] = 'NOT AJAX';
        }

        return new JsonResponse($arr);
    }
}
