<?php

namespace Custom\FchatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/secured")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {

    $authenticationUtils = $this->get('security.authentication_utils');

    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('CustomFchatBundle:Secured:login2.html.twig',
                        array(
            'last_username' => $lastUsername,
            'error'         => $error,
                            )
        );
    }


    /**
     * @Route("/say", name="secured_echo")
     */
    public function sayAction()
    {

            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            
                 $user = $this->getUser();
                 $username = $user->getUsername();
            }
                else

                   $username= 'anon';

        return $this->render('CustomFchatBundle:Secured:say.html.twig',
                            array(

                                'username' => $username
                                 )
                            );
    }

}
