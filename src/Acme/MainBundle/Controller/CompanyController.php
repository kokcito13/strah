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
}
