<?php

namespace Acme\UserBundle\Controller;

use Acme\UserBundle\Form\UserType;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/users/{id}", name="user_show")
     * @Template()
     */
    public function showAction(User $user)
    {
        $loginUser = $this->getUser();
//        if (!$user) {
//            throw $this->createNotFoundException('Данную статью в этой категории мы неможем найти!');
//        }

        $canEditing = false;
        if ($loginUser && $loginUser->getId() == $user->getId()) {
            $canEditing = true;
        }

        return array(
            'user' => $user,
            'canEditing' => $canEditing,
        );
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @Template()
     */
    public function editAction(User $user, Request $request)
    {
        $loginUser = $this->getUser();
        if (!$loginUser || $loginUser->getId() != $user->getId()) {
            throw $this->createNotFoundException('Данную статью в этой категории мы неможем найти!');
        }

        $form = $this->createForm(new UserType(), $this->getUser());
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->get('reset')->isClicked()) {
                return $this->redirect($this->generateUrl('user_show', array('id'=>$user->getId())));
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id'=>$user->getId())));
        }

        return array(
            'user' => $user,
            'canEditing' => ($loginUser && $loginUser->getId() == $user->getId()),
            'form' => $form->createView()
        );
    }
}
