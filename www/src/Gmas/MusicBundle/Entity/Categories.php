<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categories
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\CategoriesRepository")
 */
class Categories
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
     * @var boolean
     *
     * @ORM\Column(name="statut", type="boolean")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity="Gmas\MusicBundle\Entity\Song", mappedBy="category")
     */
    private $songs;

    /**
    * @ORM\OneToOne(targetEntity="Gmas\MusicBundle\Entity\StatsCategory", cascade={"remove"})
    */
    private $stats;


    public function __construct() {
        $this->songs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Categories
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
     * Set statut
     *
     * @param integer $statut
     * @return Categories
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
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * Set stats
     *
     * @param integer $stats
     * @return StatsCategory
     */
    public function setStats(\Gmas\MusicBundle\Entity\StatsCategory $stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return integer
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Add songs
     *
     * @param \Gmas\MusicBundle\Entity\Song $songs
     * @return Categories
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
