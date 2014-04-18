<?php

namespace Gmas\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gmas\UserBundle\Entity\User;
use Gmas\UserBundle\Entity\UserRepository;

class UserController extends Controller {
  public function admin_listAction() {
    $em = $this->getDoctrine()->getManager();

    $users = $em->getRepository('GmasUserBundle:User')->findAll();

    return $this->render('GmasUserBundle:Admin:admin_list.html.twig', array(
      'users' => $users
    ));
  }

  public function admin_list_adminAction() {
    $em = $this->getDoctrine()->getManager();

    $users = $em->getRepository('GmasUserBundle:User')->myFindByRole('ADMIN');

    return $this->render('GmasUserBundle:Admin:admin_list_admin.html.twig', array(
      'users' => $users
    ));
  }

  public function admin_viewAction($userid) {
    $em = $this->getDoctrine()->getManager();

    $user = $em->getRepository('GmasUserBundle:User')->find($userid);

    return $this->render('GmasUserBundle:Admin:admin_view.html.twig', array(
      'user' => $user
    ));
  }

  public function admin_deleteAction($userid) {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('GmasUserBundle:User')->find($userid);
    $session = $this->getRequest()->getSession();

    $em->remove($user);
    $em->flush();

    $session->getFlashBag()->add('success', 'L\'utilisateur a bien été supprimé !');

    return $this->redirect($this->generateUrl('gmas_user_admin_users_list'));
  }



  public function admin_validateAction($userid) {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('GmasUserBundle:User')->find($userid);
    $session = $this->getRequest()->getSession();

    $enabled = $user->getEnabled();

    if($user->getEnabled() == 1)
      $user->setEnabled(0);
    else
      $user->setEnabled(1);

    $em->persist($user);
    $em->flush();

    if($enabled == 0)
      $session->getFlashBag()->add('success', 'L\'utilisateur a bien été validé !');
    else
      $session->getFlashBag()->add('success', 'L\'utilisateur a bien été dévalidé !');

    return $this->redirect($this->generateUrl('gmas_user_admin_users_list'));

  }

  public function admin_createAction() {
    $form = $this->container->get('fos_user.registration.form');

    $em = $this->getDoctrine()->getManager();

    $users = $em->getRepository('GmasUserBundle:User')->findAll();

    return $this->render('GmasUserBundle:Admin:admin_create.html.twig', array(
      'form' => $form->createView(),
    ));
  }
}
