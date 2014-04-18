<?php

namespace Gmas\MusicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gmas\MusicBundle\Entity\Song;
use Gmas\MusicBundle\Entity\UserSong;
use Gmas\MusicBundle\Entity\StatsSong;

/**
 * Ajax controller.
 *
 * @Route("/ajax")
 */
class AjaxController extends Controller
{

    public function updateDataAction(){
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();

        $youtubeid = $request->request->get('youtubeid');
        $category = $em->getRepository('GmasMusicBundle:Categories')->find($request->request->get('category'));

        $song = new Song();

        $tmplink = explode('v=', $youtubeid);
        if(isset($tmplink[1])) {
            $tmplink2 = explode('&', $tmplink[1]);
            if(isset($tmplink2[0])) {
                $song->setYoutubeid($tmplink2[0]);
                $song->setCategory($category);

                if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $song->setUser($this->container->get('security.context')->getToken()->getUser());
                    if($this->get('security.context')->isGranted('ROLE_ADMIN'))
                        $song->setStatut(1);
                    else
                        $song->setStatut(0);
                }
                else {
                    $song->setStatut(0);
                }

                $song->setDeadlink(0);

                $xml = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$song->getYoutubeid());
                preg_match('#<title(.*?)>(.*)<\/title>#is', $xml, $resultTitre);
                if(isset($resultTitre[count($resultTitre) - 1])) {
                    $song->setName($resultTitre[count($resultTitre) - 1]);

                    $song_verif = $em->getRepository('GmasMusicBundle:Song')->findBy(array('youtubeid' => $tmplink2, 'category' => $category));

                    $em = $this->getDoctrine()->getManager();

                    $stats = new StatsSong();
                    $em->persist($stats);

                    if(count($song_verif) == 0) {
                        $song->setStats($stats);
                        $em->persist($song);

                        $em->flush();
                        $flash = 'success';
                        $text = '<strong>'.$song->getName().'</strong> a bien été proposé dans la playlist <strong>'.$category->getName().'</strong><br>La musique sera disponible lorsqu\'elle sera validée !';
                    }
                    else {
                        $flash = 'error';
                        $text = '<strong>'.$song->getName().'</strong> est déjà disponible dans cette playlist !';
                    }
                }
                else {
                    $flash = 'error';
                    $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
                }
            }
            else {
                $flash = 'error';
                $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
            }
        }
        else {
            $flash = 'error';
            $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
        }

        $response = array("code" => 100, "success" => true, "flash" => $flash, "text" => $text);

        return new Response(json_encode($response));
    }


    public function deadlinkAction($youtubeid){
        $request = $this->container->get('request');

        $em = $this->getDoctrine()->getManager();

        $songs = $em->getRepository('GmasMusicBundle:Song')->findByYoutubeid($youtubeid);

        foreach($songs as $k => $v) {
            $v->setDeadlink(1);
            $em->persist($v);
        }

        $em->flush();
        $response = array("code" => 100, "success" => true, "youtubeid" => $youtubeid);

        return new Response(json_encode($response));
    }

    public function setListenedSongAction(){
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();

        $song_id = $request->request->get('id');
        $song = new Song();

        if($song_id != null) {
            $song = $em->getRepository('GmasMusicBundle:Song')->find($song_id);
            $song->getStats()->setViews(1);
            $em->persist($song);
            $em->flush();
        }
        else
            return new Response(json_encode(array("code" => 100, "success" => false)));

        $response = array("code" => 100, "success" => true);
        return new Response(json_encode($response));
    }

    public function setNextedSongAction(){
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();

        $song_id = $request->request->get('id');
        $song = new Song();

        if($song_id != null) {
            $song = $em->getRepository('GmasMusicBundle:Song')->find($song_id);
            $song->getStats()->setNexts(1);
            $em->persist($song);
            $em->flush();
        }
        else
            return new Response(json_encode(array("code" => 100, "success" => false)));

        $response = array("code" => 100, "success" => true);
        return new Response(json_encode($response));
    }

    public function getNextYoutubeIdAction(){
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();

        $song_id = $request->request->get('id');
        $playlist = unserialize($session->get('playlist'));

        if($song_id != 0) {
            if(isset($playlist[array_search($song_id, $playlist) + 1]))
                $next_song = $playlist[array_search($song_id, $playlist) + 1];
            else
                return new Response(json_encode(array("code" => 100, "success" => false, "song_id" => 0, "youtubeid" => 0)));
        }

        $song = $em->getRepository('GmasMusicBundle:Song')->find($next_song);

        $response = array("code" => 100, "success" => true, "song_id" => $song->getId(), "youtubeid" => $song->getYoutubeid(), "song_name" => $song->getName());
        return new Response(json_encode($response));
    }

    public function getPreviousYoutubeIdAction(){
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();

        $song_id = $request->request->get('id');
        $playlist = unserialize($session->get('playlist'));

        if($song_id != 0) {
            if(isset($playlist[array_search($song_id, $playlist) - 1]))
                $previous_song = $playlist[array_search($song_id, $playlist) - 1];
            else
                return new Response(json_encode(array("code" => 100, "success" => false, "song_id" => 0, "youtubeid" => 0)));
        }

        $song = $em->getRepository('GmasMusicBundle:Song')->find($previous_song);

        $response = array("code" => 100, "success" => true, "song_id" => $song->getId(), "youtubeid" => $song->getYoutubeid(), "song_name" => $song->getName());
        return new Response(json_encode($response));
    }

    public function likeAction($youtubeid){
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $request = $this->container->get('request');

            $em = $this->getDoctrine()->getManager();

            $songs = $em->getRepository('GmasMusicBundle:Song')->findByYoutubeid($youtubeid);

            foreach($songs as $k => $v) {
                $userSong = new UserSong();
                $userSong->setUser($this->container->get('security.context')->getToken()->getUser());
                $userSong->setSong($v);
                $userSong->setPreference(1); // Favoris
                $em->persist($userSong);
            }

            $em->flush();
            $response = array("code" => 100, "success" => true, "flash" => "success", "text" => "La musique a bien été ajoutée à vos favoris !");
        }
        else
            $response = array("code" => 0, "success" => false, "flash" => "error", "text" => "Vous devez être connecté pour utiliser cette fonctionnalité !");

        return new Response(json_encode($response));
    }


    public function hateAction($youtubeid){
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $request = $this->container->get('request');

            $em = $this->getDoctrine()->getManager();

            $songs = $em->getRepository('GmasMusicBundle:Song')->findByYoutubeid($youtubeid);

            foreach($songs as $k => $v) {
                $userSong = new UserSong();
                $userSong->setUser($this->container->get('security.context')->getToken()->getUser());
                $userSong->setSong($v);
                $userSong->setPreference(-1); // Ne plus entendre
                $em->persist($userSong);
            }

            $em->flush();
            $response = array("code" => 100, "success" => true, "flash" => "success", "text" => "La musique a bien été ajoutée aux musiques que vous ne voulez plus entendre.<br />Vous pourrez modifier cette liste dans votre compte !");
        }
        else
            $response = array("code" => 0, "success" => false, "flash" => "error", "text" => "Vous devez être connecté pour utiliser cette fonctionnalité !");

        return new Response(json_encode($response));
    }


    public function admin_addSongAction() {
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();

        $youtubeid = $request->request->get('youtubeid');
        $category = $em->getRepository('GmasMusicBundle:Categories')->find($request->request->get('category'));

        $song = new Song();

        $tmplink = explode('v=', $youtubeid);
        if(isset($tmplink[1])) {
            $tmplink2 = explode('&', $tmplink[1]);
            if(isset($tmplink2[0])) {
                $song->setYoutubeid($tmplink2[0]);
                $song->setCategory($category);
                $song->setUser($this->container->get('security.context')->getToken()->getUser());
                $song->setStatut(1);
                $song->setDeadlink(0);

                $xml = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$song->getYoutubeid());
                preg_match('#<title(.*?)>(.*)<\/title>#is', $xml, $resultTitre);

                if(isset($resultTitre[count($resultTitre) - 1])) {
                    $song->setName($resultTitre[count($resultTitre) - 1]);

                    $song_verif = $em->getRepository('GmasMusicBundle:Song')->findBy(array('youtubeid' => $tmplink2, 'category' => $category));

                    $em = $this->getDoctrine()->getManager();

                    $stats = new StatsSong();
                    $em->persist($stats);

                    if(count($song_verif) == 0) {
                        $song->setStats($stats);
                        $em->persist($song);

                        $em->flush();
                        $flash = 'success';
                        $text = '<strong>'.$song->getName().'</strong> a bien été ajoutée dans la playlist <strong>'.$category->getName().'</strong>';
                    }
                    else {
                        $flash = 'error';
                        $text = '<strong>'.$song->getName().'</strong> est déjà disponible dans cette playlist !';
                    }
                }
                else {
                    $flash = 'error';
                    $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
                }
            }
            else {
                $flash = 'error';
                $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
            }
        }
        else {
            $flash = 'error';
            $text = '<strong>Le lien Youtube n\'est pas valide !</strong>';
        }

        $response = array("code" => 100, "success" => true, "flash" => $flash, "text" => $text);

        return new Response(json_encode($response));
    }


    public function admin_deadlinks_replaceAction($youtubeid, $youtubeid_new) {
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('GmasMusicBundle:Song')->findByYoutubeid($youtubeid);

        $nbSongs = count($songs);
        $nbReplace = 0;
        foreach ($songs as $k => $v) {
            $v->setYoutubeid($youtubeid_new);
            $v->setDeadlink(0);
            $em->persist($v);
            $nbReplace++;
        }
        $em->flush();

        if($nbSongs == $nbReplace)
            $response = array("success" => true, "flash" => "success", "text" => "La musique a bien été remplacée dans toutes les playlists !");
        else
            $response = array("success" => false, "flash" => "error", "text" => "Un problème est survenu.");

        return new Response(json_encode($response));
    }


    public function admin_deadlinks_keepAction($youtubeid) {
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('GmasMusicBundle:Song')->findByYoutubeid($youtubeid);

        $nbSongs = count($songs);
        $nbReplace = 0;
        foreach ($songs as $k => $v) {
            $v->setDeadlink(0);
            $em->persist($v);
            $nbReplace++;
        }
        $em->flush();

        if($nbSongs == $nbReplace)
            $response = array("success" => true, "flash" => "success", "text" => "La musique a bien été revalidée dans toutes les playlists !");
        else
            $response = array("success" => false, "flash" => "error", "text" => "Un problème est survenu.");

        return new Response(json_encode($response));
    }


}
