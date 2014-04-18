<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Song
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\SongRepository")
 */
class Song
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="youtubeid", type="string", length=255)
     */
    private $youtubeid;

    /**
     * @ORM\ManyToOne(targetEntity="Gmas\MusicBundle\Entity\Categories", inversedBy="songs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Gmas\UserBundle\Entity\User", inversedBy="songs")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="smallint")
     */
    private $statut;

    /**
     * @var integer
     *
     * @ORM\Column(name="deadlink", type="integer")
     */
    private $deadlink;

    /**
    * @ORM\OneToOne(targetEntity="Gmas\MusicBundle\Entity\StatsSong", cascade={"persist"})
    */
    private $stats;

    public function __construct() {
        $user = null;
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
     * Set name
     *
     * @param string $name
     * @return Song
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set youtubeid
     *
     * @param string $youtubeid
     * @return Song
     */
    public function setYoutubeid($youtubeid)
    {
        $this->youtubeid = $youtubeid;

        return $this;
    }

    /**
     * Get youtubeid
     *
     * @return string
     */
    public function getYoutubeid()
    {
        return $this->youtubeid;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     * @return Song
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set deadlink
     *
     * @param integer $statut
     * @return Song
     */
    public function setDeadlink($deadlink)
    {
        $this->deadlink = $deadlink;

        return $this;
    }

    /**
     * Get deadlink
     *
     * @return integer
     */
    public function getDeadlink()
    {
        return $this->deadlink;
    }

    /**
     * Set category
     *
     * @param \Gmas\MusicBundle\Entity\Categories $category
     * @return Song
     */
    public function setCategory(\Gmas\MusicBundle\Entity\Categories $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Gmas\MusicBundle\Entity\Categories
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Gmas\UserBundle\Entity\User $user
     * @return Song
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
     * Set stats
     *
     * @param \stdClass $stats
     * @return StatsSong
     */
    public function setStats(\Gmas\MusicBundle\Entity\StatsSong $stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return \stdClass
     */
    public function getStats()
    {
        return $this->stats;
    }
}
