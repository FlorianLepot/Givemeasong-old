<?php

namespace Gmas\MusicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gmas\MusicBundle\Entity\Song;
use Gmas\MusicBundle\Entity\StatsSong;
use Gmas\MusicBundle\Form\SongType;
use Gmas\MusicBundle\Form\SongSmallType;

/**
 * Song controller.
 *
 * @Route("/song")
 */
class SongController extends Controller
{
    /**
     * Lists all Song entities.
     *
     * @Route("/", name="song")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        return array(
            'list_categories' => $entities,
        );
    }

    /**
    * Play the song
    *
    * @Route("/listen/{song_id}", name="song_listen")
    * @Method("GET")
    * @Template()
    */
    public function listenAction($song_id = 0, $song_nexted = null) {
        $em = $this->getDoctrine()->getManager();

        $song_prev = new Song();
        if($song_nexted != null) {
            $song_prev = $em->getRepository('GmasMusicBundle:Song')->find($song_nexted);
            $song_prev->getStats()->setNexts(1);
            $em->persist($song_prev);
            $em->flush();
        }

        $newSong = new Song();
        $form = $this->createForm(new SongSmallType(), $newSong);

        $song = $em->getRepository('GmasMusicBundle:Song')->find($song_id);

        $us = false;
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $userSong = $em->getRepository('GmasMusicBundle:UserSong')->findBy(array('song' => $song, 'user' => $user));
            if(!empty($userSong))
                $us = true;
        }

        $session = $this->getRequest()->getSession();
        $playlist = unserialize($session->get('playlist'));

        if($song_id != 0) {
            if(isset($playlist[array_search($song_id, $playlist) + 1]))
                $next_song = $playlist[array_search($song_id, $playlist) + 1];
            else
                $next_song = 0;
            if(isset($playlist[array_search($song_id, $playlist) - 1]))
                $prev_song = $playlist[array_search($song_id, $playlist) - 1];
            else
                $prev_song = 0;

            $song->getStats()->setViews(1);
            $em->persist($song);
            $em->flush();
        }
        else {
            return $this->redirect($this->generateUrl('gmas_music_homepage'));
        }

        return $this->render('GmasMusicBundle:Song:listen.html.twig', array(
            'form' => $form->createView(),
            'song' => $song,
            'next_song' => $next_song,
            'prev_song' => $prev_song,
            'userSong' => $us,
            'id_session' => $session->getId()
        ));
    }

    /**
     * Creates a new Song entity.
     *
     * @Route("/", name="song_create")
     * @Method("POST")
     * @Template("GmasMusicBundle:Song:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Song();
        $form = $this->createForm(new SongType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('song_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    public function getCreateFormAction() {
        $em = $this->getDoctrine()->getManager();

        $entity  = new Song();
        $form = $this->createForm(new SongSmallType(), $entity);


        return $this->render('GmasMusicBundle:Song:getCreateForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Song entity.
     *
     * @Route("/new", name="song_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Song();
        $form   = $this->createForm(new SongType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Song entity.
     *
     * @Route("/{id}", name="song_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Song')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Song entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Song entity.
     *
     * @Route("/{id}/edit", name="song_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Song')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Song entity.');
        }

        $editForm = $this->createForm(new SongType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Song entity.
     *
     * @Route("/{id}", name="song_update")
     * @Method("PUT")
     * @Template("GmasMusicBundle:Song:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GmasMusicBundle:Song')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Song entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SongType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('song_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Deletes a Song entity.
     *
     * @Route("/{id}", name="song_delete")
     * @Method("DELETE")
     */
    public function admin_deleteAction($id)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $song = $em->getRepository('GmasMusicBundle:Song')->find($id);

        $em->remove($song);
        $em->flush();

        $session->getFlashBag()->add('success', 'La musique a bien été supprimée !');

        return $this->redirect($this->generateUrl('gmas_music_admin_songs_list'));
    }

    /**
     * Lists all Song entities.
     *
     * @Route("/admin", name="song_admin_list")
     * @Method("POST")
     * @Template()
     */
    public function admin_listAction() {
        $session = $this->getRequest()->getSession();
        $request = $this->get('request');

        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('GmasMusicBundle:Song')->findAll();
        $categories = $em->getRepository('GmasMusicBundle:Categories')->findAll();

        $song = new Song();
        $form = $this->createForm(new SongSmallType(), $song);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            $tmplink = explode('v=', $song->getYoutubeid());
            $tmplink2 = explode('&', $tmplink[1]);
            $song->setYoutubeid($tmplink2[0]);
            $song->setStatut(1);
            $song->setUser($this->container->get('security.context')->getToken()->getUser());

            $xml = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$song->getYoutubeid());
            preg_match('#<title(.*?)>(.*)<\/title>#is', $xml, $resultTitre);
            $song->setName($resultTitre[count($resultTitre)-1]);
            $song->setDeadlink(0);

            $stats = new StatsSong();
            $em->persist($stats);

            if ($form->isValid()) {
                $song->setStats($stats);
                $em->persist($song);

                $em->flush();
                $session->getFlashBag()->add('success', 'La musique a bien été ajoutée !');
            }
            else
                $session->getFlashBag()->add('error', 'Un problème est survenu.');

            return $this->redirect($this->generateUrl('gmas_music_admin_songs_list'));
        }

        return $this->render('GmasMusicBundle:Song:admin_list.html.twig', array(
            'form' => $form->createView(),
            'form2' => $form->createView(),
            'songs' => $songs,
            'categories' => $categories
        ));
    }

    public function admin_indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GmasMusicBundle:Categories')->findAll();
        $users = $em->getRepository('GmasUserBundle:User')->findAll();
        $songs = $em->getRepository('GmasMusicBundle:Song')->findAll();

        return $this->render('GmasMusicBundle:Song:admin_index.html.twig', array(
            'users' => $users,
            'songs' => $songs
        ));
    }

    public function admin_deadlinksAction()
    {
        $em = $this->getDoctrine()->getManager();

        $songs = $em->getRepository('GmasMusicBundle:Song')->findByDeadlink(1);

        foreach ($songs as $k => $v) {
            //echo '['.$v->getId().'] Hold : '.$v->getName().' : '.$v->getYoutubeid().'<br />';
            $yt = new \Zend_Gdata_YouTube();
            $query = $yt->newVideoQuery();
            $query->videoQuery = str_replace('-', ' ', $v->getName()).'';
            $query->startIndex = 0;
            $query->maxResults = 10;
            $query->orderBy = 'relevance';
            //echo $query->queryUrl . "<br />";

            $videoFeed = $yt->getVideoFeed($query);

            // Quand -1 -> Proposer une liste de vidéos
            // Quand 0 aussi ?
            foreach ($videoFeed as $videoEntry) {
                $title = $videoEntry->getVideoTitle();
                if($this->compareTitles($v->getName(), $title) != -1) {
                    $newSong[$v->getId()]['name'] = $videoEntry->getVideoTitle();
                    $newSong[$v->getId()]['youtubeid'] = $videoEntry->getVideoId();
                    break;
                }
            }
            if(empty($newSong[$v->getId()])) {
                $newSong[$v->getId()]['name'] = 'Aucune vidéo trouvée';
                $newSong[$v->getId()]['youtubeid'] = 0;
            }
            //echo '['.$v->getId().'] New : '.$newSong[$v->getId()]['name'].'<br />';
            //echo '-------------<br />';

        }

        return $this->render('GmasMusicBundle:Song:admin_deadlinks.html.twig', array(
            'songs' => $songs,
            'newSong' => $newSong
        ));
    }

    public function compareTitles($original, $new) {
        $params = array('remix' => false, 'live' => false);

        // On traite les variables envoyées
        $original = strtolower($original);
        $new = strtolower($new);
        $original = str_replace('-', ' ', $original);
        $new = str_replace('-', ' ', $new);
        if(stripos($original, 'remix') !== false)
            $params['remix'] = true;
        if(stripos($original, 'live') !== false || stripos($original, '@') !== false || preg_match('#(19|20)[0-9]{2}#', $original))
            $params['live'] = true;
        $original = preg_replace('/\(.*\)/', '', $original);

        // On crée un tableau avec les mots de $original et $new
        $aOrigine = explode(' ', $original);
        $aNew = explode(' ', $new);

        // Comparaison
        $similitude = 0;
        $aNewRes = $aNew;

        // On compare $original et $new
        foreach ($aOrigine as $k => $v) {
            if(in_array($v, $aNew)) {
                $similitude++;
                unset($aNewRes[array_search($v, $aNewRes)]);
            }
        }

        // Si le titre est identique (ou presque), on le prend
        if(($similitude / count($aOrigine)) * 100 >= 80)
            return 1;

        // On regarde si dans les mots restants il n'y a pas de mots dont on ne veut pas (live, remix)
        foreach ($aNewRes as $k => $v) {
            if((stripos($v, 'remix') === false || $params['remix'] == true) && (stripos($v, 'live') === false || $params['live'] == true) && (stripos($v, '@') === false || $params['live'] == true) && !preg_match('#(19|20)[0-9]{2}#', $v))
                return 0;
        }

        return -1;

    }

    /**
     * Creates a form to delete a Song entity by id.
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
}
