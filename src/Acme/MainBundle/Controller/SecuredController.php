<?php

namespace Acme\MainBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/secured")
 */
class SecuredController extends Controller
{
    /**
     * @Route("/social/registration", name="_secured_social_registration")
     * @Method({"POST"})
     */
    public function socialRegistrationAction(Request $request)
    {
        $resp = array();
        if ($request->isXmlHttpRequest()) {
            $regSocialService = $this->get('registering.user.social');
//            try{
                $user = $regSocialService->registration($request->request->all());


                $security = $this->get('security.context');
                $providerKey = 'main';
                $roles = $user->getRoles();
                $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, $providerKey, $roles);
                $security->setToken($token);

               //need make user logined

//                $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, 'main');
//                $this->get('security.context')->setToken($token);
//                $this->get('session')->set('_security_main',serialize($token));

                $resp['user_id'] = $user->getId();

                $resp['success'] = true;
//            } catch (\Exception $e) {
//                $resp['error'] = 'Some error';
//            }
        } else {
            $resp['error'] = 'Not for you';
        }

        return new JsonResponse($resp);
    }

//@Security("is_granted('ROLE_ADMIN')")
}
