<?php
// src/Gmas/UserBundle/Entity/User.php

namespace Gmas\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Gmas\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Gmas\MusicBundle\Entity\Song", mappedBy="user")
     */
    private $songs;

    /**
     * @var datetime
     *
     * @ORM\Column(name="registration", type="datetime")
     */
    private $registration;

    public function __construct()
    {
        parent::__construct();
        $this->songs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registration = new \DateTime();
    }

    /**
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @return DateTime
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @return int
     */
    public function getEnabled() {
        return $this->enabled;
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
     * Set registration
     *
     * @param \DateTime $registration
     * @return User
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Add songs
     *
     * @param \Gmas\MusicBundle\Entity\Song $songs
     * @return User
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
}
