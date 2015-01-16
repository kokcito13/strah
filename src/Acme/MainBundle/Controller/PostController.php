<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\PostRepository;
use Acme\MainBundle\Service\Perelink;
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

        if (!$entity) {
            throw $this->createNotFoundException('Данную статью мы неможем найти!');
        }

        $entities = $postRepo->findOther($entity);
        
        $entity->oneMoreView();
        $em->persist($entity);
        $em->flush();

        $text = $entity->getText();

        $cacheKey = 'post_pereink_'.$entity->getId();
        $cache = $this->get('cache.m');
        $result = $cache->fetch($cacheKey);
        if (!$result) {
            $baseurl = $this->getRequest()->getScheme() .
                '://' . $this->getRequest()->getHttpHost() .
                $this->getRequest()->getBasePath().
                $this->getRequest()->getPathInfo();

            /** @var Perelink $perelink */
            $perelink = $this->get('binet.perelink');
            $perelink->getInfo($baseurl);
            $text = $perelink->updateText($text);
            $links = $perelink->getLinksAfter();

            $cache->save($cacheKey, json_encode(array(0=>$text, 1=>$links)), (2*24*60*60));
        } else {
            $result = json_decode($result, true);
            $text = $result[0];
            $links = $result[1];
        }

        $entity->setText($text);

        return array(
            'entity'        => $entity,
            'entities'      => $entities,
            'links' => $links
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
