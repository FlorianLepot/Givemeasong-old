<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSong
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\UserSongRepository")
 */
class UserSong
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Gmas\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Gmas\MusicBundle\Entity\Song")
     */
    private $song;

    /**
     * @var integer
     *
     * @ORM\Column(name="preference", type="integer")
     */
    private $preference;


    public function setUser(\Gmas\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setSong(\Gmas\MusicBundle\Entity\Song $song)
    {
        $this->song = $song;
    }

    public function getSong()
    {
        return $this->song;
    }

    public function setPreference($preference)
    {
        $this->preference = $preference;
    }
    
    public function getPreference()
    {
        return $this->preference;
    }
}
