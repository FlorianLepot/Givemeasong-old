<?php

namespace Gmas\MusicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gmas\MusicBundle\Entity\Categories;
use Gmas\MusicBundle\Entity\StatsCategory;
use Gmas\MusicBundle\Entity\Playlist;
use Gmas\MusicBundle\Form\CategoriesType;

/**
 * Categories controller.
 *
 * @Route("/categories")
 */
class CategoriesController extends Controller
{
    /**
     * Lists all Categories entities.
     *
     * @Route("/", name="categories")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
    * Redirect user on the right playlist
    *
    * @Route("/listen/{category_id}", name="categories_listen")
    * @Method("GET")
    * @Template()
    */
    public function listenAction($category_id) {
        $em = $this->getDoctrine()->getManager();

        if($category_id == 0) {
            $request = $this->get('request');
            $categories = $request->request->get('playlists');
            if(!empty($categories))
                $songs = $em->getRepository('GmasMusicBundle:Song')->findBy(array('statut' => 1, 'category' => $categories, 'deadlink' => 0));
            else {
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('error', 'Vous devez sélectionner au moins une catégorie !');
                return $this->redirect($this->generateUrl('gmas_music_homepage'));
            }
        }
        else {
            $category = $em->getRepository('GmasMusicBundle:Categories')->find($category_id);
            if($category === null) {
                throw $this->createNotFoundException('Categories[category='.$category.'] inexistante');
            }

            $category->getStats()->setViews(1);
            $em->persist($category);
            $em->flush();

            $songs = $em->getRepository('GmasMusicBundle:Song')->findBy(array('category' => $category->getId(), 'statut' => 1));
        }

        $playlist = array();
        for($i = 0; $i < count($songs); $i++) {
            do {
                $number = rand(1, count($songs));
            } while(in_array($songs[$number - 1]->getId(), $playlist));

            $playlist[$i] = $songs[$number - 1]->getId();
        }

        $session = $this->getRequest()->getSession();
        $session->set('playlist', serialize($playlist));

        return $this->redirect($this->generateUrl('gmas_music_songs_listen', array('song_id' => $playlist[0])));
    }

    /**
    * Redirect user on the right playlist
    *
    * @Route("/{categoryId}", name="categories_start", options={"expose"=true})
    * @Method("GET")
    * @Template()
    */
    public function startAction($categoryId) {
        $em = $this->getDoctrine()->getManager();

        if($categoryId == 0) {
            $request = $this->get('request');
            $categories = $request->request->get('playlists');
            if(!empty($categories))
                $songs = $em->getRepository('GmasMusicBundle:Song')->findBy(array('statut' => 1, 'category' => $categories, 'deadlink' => 0));
            else {
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('error', 'Vous devez sélectionner au moins une catégorie !');
                return $this->redirect($this->generateUrl('gmas_music_homepage_content'));
            }
        }
        else {
            $category = $em->getRepository('GmasMusicBundle:Categories')->find($categoryId);
            if($category === null) {
                throw $this->createNotFoundException('Categories[category='.$category.'] inexistante');
            }

            $category->getStats()->setViews(1);
            $em->persist($category);
            $em->flush();

            $songs = $em->getRepository('GmasMusicBundle:Song')->findBy(array('category' => $category->getId(), 'statut' => 1));
        }

        $playlist = new Playlist();
        foreach ($songs as $song) {
            $playlist->addSong($song);
        }

        if($this->getUser() != null) {
            $playlist->setUser($this->getUser());
        }

        $em->persist($playlist);
        $em->flush();

        $session = $this->get('session');
        $session->set('playlist', $playlist);

        return $this->redirect($this->generateUrl('gmas_music_listen_content', array('playlistId' => $playlist->getId(), 'songId' => $playlist->getSongs()[0]->getId())));
    }

    /**
     * Creates a new Categories entity.
     *
     * @Route("/", name="categories_create")
     * @Method("POST")
     * @Template("GmasMusicBundle:Categories:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Categories();
        $form = $this->createForm(new CategoriesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Categories entity.
     *
     * @Route("/new", name="categories_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Categories();
        $form   = $this->createForm(new CategoriesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Categories entity.
     *
     * @Route("/{id}", name="categories_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Categories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categories entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Categories entity.
     *
     * @Route("/{id}/edit", name="categories_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Categories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categories entity.');
        }

        $editForm = $this->createForm(new CategoriesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Categories entity.
     *
     * @Route("/{id}", name="categories_update")
     * @Method("PUT")
     * @Template("GmasMusicBundle:Categories:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Categories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categories entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoriesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Categories entity.
     *
     * @Route("/{id}", name="categories_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GmasMusicBundle:Categories')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categories entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categories'));
    }

    /**
     * Creates a form to delete a Categories entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Finds and displays a Categories entity.
     *
     * @Template()
     * @Template("GmasMusicBundle:Categories:getCategoriesTemplate.html.twig")
     */
    public function getCategoriesTemplateAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        return array(
            'categories'      => $entities,
        );
    }

    public function admin_listAction() {
        $session = $this->getRequest()->getSession();
        $request = $this->get('request');

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        $category = new Categories();
        $form = $this->createForm(new CategoriesType(), $category);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            $em = $this->getDoctrine()->getManager();

            $stats = new StatsCategory();
            $em->persist($stats);

            if ($form->isValid()) {
                $category->setStats($stats);
                $em->persist($category);

                $em->flush();
                $session->getFlashBag()->add('success', 'La catégorie a bien été ajoutée !');
            }
            else {
                print_r($form->getErrors());
                $session->getFlashBag()->add('error', 'Un problème est survenu : ');
            }

            return $this->redirect($this->generateUrl('gmas_music_admin_categories_list'));
        }

        return $this->render('GmasMusicBundle:Categories:admin_list.html.twig', array(
            'form' => $form->createView(),
            'categories' => $categories
        ));
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="categories_delete")
     * @Method("DELETE")
     */
    public function admin_deleteAction($id)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('GmasMusicBundle:Categories')->find($id);

        $em->remove($category);
        $em->flush();

        $session->getFlashBag()->add('success', 'La catégorie a bien été supprimée !');

        return $this->redirect($this->generateUrl('gmas_music_admin_categories_list'));
    }
}
