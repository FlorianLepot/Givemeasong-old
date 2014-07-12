<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Playlist
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\PlaylistRepository")
 */
class Playlist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Gmas\UserBundle\Entity\User", inversedBy="playlists")
     * @ORM\JoinColumn(nullable=true)
    */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Gmas\MusicBundle\Entity\Song", cascade={"persist"})
     */
    private $songs;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->songs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \DateTime();
        $this->token = substr(str_shuffle(MD5(microtime())), 0, 12);
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Playlist
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Playlist
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     *
     * @param \Gmas\UserBundle\Entity\User $user
     * @return Playlist
     */
    public function setUser(\Gmas\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Gmas\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add songs
     *
     * @param \Gmas\MusicBundle\Entity\Song $songs
     * @return Playlist
     */
    public function addSong(\Gmas\MusicBundle\Entity\Song $songs)
    {
        $this->songs[] = $songs;

        return $this;
    }

    /**
     * Remove songs
     *
     * @param \Gmas\MusicBundle\Entity\Song $songs
     */
    public function removeSong(\Gmas\MusicBundle\Entity\Song $songs)
    {
        $this->songs->removeElement($songs);
    }

    /**
     * Get songs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSongs()
    {
        return $this->songs;
    }
}
