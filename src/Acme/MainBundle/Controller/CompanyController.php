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
     * @Route("/company/{country_url}/{city_url}/{company_url}", name="client_company_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($country_url, $city_url, $company_url)
    {
        $em = $this->getDoctrine()->getManager();
        $country = $em->getRepository('AcmeMainBundle:Country')->findOneByUrl($country_url);
        $city = $em->getRepository('AcmeMainBundle:City')->findOneBy(array(
            'url' => $city_url,
            'country' => $country
        ));
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
     * @Route("/company/{country_url}/{city_url}/{company_url}/comments", name="client_company_show_comments")
     * @Method("GET")
     * @Template()
     */
    public function showCommentsAction($country_url, $city_url, $company_url)
    {
        $em = $this->getDoctrine()->getManager();
        $country = $em->getRepository('AcmeMainBundle:Country')->findOneByUrl($country_url);
        $city = $em->getRepository('AcmeMainBundle:City')->findOneBy(array(
            'url' => $city_url,
            'country' => $country
        ));
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
     * @Route("/company/{country_url}/{city_url}/{company_url}/save_comment", name="client_company_save_comment")
     * @Method("POST")
     */
    public function saveCommentAction(Request $request, $country_url, $city_url, $company_url)
    {
        $arr = array();
        if ($request->isXmlHttpRequest()) {


            $em = $this->getDoctrine()->getManager();
            $country = $em->getRepository('AcmeMainBundle:Country')->findOneByUrl($country_url);
            $city = $em->getRepository('AcmeMainBundle:City')->findOneBy(array(
                'url' => $city_url,
                'country' => $country
            ));
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
