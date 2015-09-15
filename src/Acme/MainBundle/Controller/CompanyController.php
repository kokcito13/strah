<?php

namespace Acme\MainBundle\Controller;

use Acme\MainBundle\Entity\Comment;
use Acme\MainBundle\Entity\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\MainBundle\Entity\Post;
use Acme\MainBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Post controller.
 *
 * @Route("/")
 */
class CompanyController extends Controller
{
    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{city_url}/companies/{company_url}", name="client_company_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($company_url)
    {
        $em = $this->getDoctrine()->getManager();

        $city = $this->get('city.service')->getCity();
        if (!$city) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти');
        }

        $companyRepo = $em->getRepository('AcmeMainBundle:Company');
        $entity = $companyRepo->findOneBy(
            array(
                'city' => $city,
                'url' => $company_url
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти!');
        }

        return array(
            'entity'  => $entity,
        );
    }

    /**
     *
     * @Route("/{city_url}/companies/{company_url}/comments", name="client_company_show_comments")
     * @Method("GET")
     * @Template()
     */
    public function showCommentsAction($company_url)
    {
        $em = $this->getDoctrine()->getManager();

        $city = $this->get('city.service')->getCity();
        if (!$city) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти');
        }

        $companyRepo = $em->getRepository('AcmeMainBundle:Company');
        $entity = $companyRepo->findOneBy(
            array(
                'city' => $city,
                'url' => $company_url
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Данную страницу мы не можем найти!');
        }

        $title = 'Страховая компания '.$entity->getName()." - честные отзывы потребителей, честный рейтинг в городе ".$city->getName();
        $description = 'Отзывы и мнения о страховой компании '.$entity->getName()." в городе ".$city->getName().'. Отзывы которые формируют мнение и честный рейтинг  о компании ';

        $entity->setTitle($title);
        $entity->setDescription($description);

        return array(
            'entity'  => $entity,
        );
    }

    /**
     *
     * @Route("/{city_url}/companies/{company_url}/save_comment", name="client_company_save_comment")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     */
    public function saveCommentAction(Request $request, $company_url)
    {
        $arr = array();
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $city = $this->get('city.service')->getCity();

            if (!$city) {
                throw $this->createNotFoundException('Данную страницу мы не можем найти');
            }

            $companyRepo = $em->getRepository('AcmeMainBundle:Company');
            $entity = $companyRepo->findOneBy(
                array(
                    'city' => $city,
                    'url' => $company_url
                )
            );


            if (!$entity) {
                throw $this->createNotFoundException('Данную страницу мы не можем найти!');
            }

            $arr['args'] = $request->request->all();

            $comment = new Comment();
            $comment->setCompany($entity);
            $comment->setName($arr['args']['name']);
            $comment->setEmail($arr['args']['email']);
            $comment->setText($arr['args']['text']);
            $comment->setRating($arr['args']['score']);

            $em->persist($comment);

            $entity->setRating($entity->getRating()+$arr['args']['score']);
            $em->persist($entity);
            $em->flush();

            $arr['success'] = true;
        } else {
            $arr['error'] = "NOT AJAX";
        }

        return new JsonResponse($arr);
    }


}
