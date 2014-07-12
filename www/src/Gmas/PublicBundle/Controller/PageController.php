<?php

namespace Gmas\PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PageController extends Controller
{
    /**
     * @Route("/index/content", name="homepage_content", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function index_contentAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        return array(
            'list_categories' => $entities,
        );
    }

    /**
     * @Route("/listen/{playlistId}/{songId}", name="listen_content", options={"expose"=true})
     * @Method("GET")
     * @Template("GmasPublicBundle:Page:listen_content.html.twig")
     */
    public function listenContentAction($playlistId, $songId)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $this->get('session');
        if ($session->get('playlist') == null || $session->get('playlist') != $playlist) {
            $playlist = $em->getRepository('GmasMusicBundle:Playlist')->find($playlistId);
            $session->set('playlist', $playlist);
        }

        $song = $em->getRepository('GmasMusicBundle:Song')->find($songId);

        return array(
            'song' => $song,
            'playlist' => $playlist,
        );
    }

    /**
     * @Route("/page/{slug}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('GmasPublicBundle:Page')->findOneBySlug($slug);

        return array(
            'page' => $page,
        );
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/admin/page/{id}/edit", name="gmas_page_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasPublicBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Page entity.
    *
    * @param Page $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Page $entity)
    {
        $form = $this->createForm(new PageType(), $entity, array(
            'action' => $this->generateUrl('gmas_page_admin_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Page entity.
     *
     * @Route("/{id}", name="gmas_page_admin_update")
     * @Method("PUT")
     * @Template("GmasPublicBundle:Page:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasPublicBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gmas_page_admin_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

}
