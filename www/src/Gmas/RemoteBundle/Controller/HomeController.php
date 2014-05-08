<?php

namespace Gmas\RemoteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/", name="remote_home", host="%remote_host%")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/security/{session}/{ident}", name="remote_client_security", host="%remote_host%")
     * @Method("GET")
     * @Template()
     */
    public function securityAction($session = null, $ident = null)
    {
        return array(
            'session' => $session,
            'ident'   => $ident
        );
    }

    /**
     * @Route("/security/auth", name="remote_client_security_auth", host="%remote_host%")
     * @Method("POST")
     */
    public function authAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('session', $request->get('session'));

        return new Response();
    }

    /**
     * @Route("/telecommande/{session}/{ident}", name="remote_client_show", host="%remote_host%")
     * @Method("GET")
     * @Template()
     */
    public function showAction($session = null, $ident = null)
    {
        $sessionSf = $this->getRequest()->getSession();
        if($sessionSf->get('session') !== $session) {
            return $this->redirect($this->generateUrl('remote_client_security'));
        }

        return array(
            'session' => $session,
            'ident'   => $ident
        );
    }
}
