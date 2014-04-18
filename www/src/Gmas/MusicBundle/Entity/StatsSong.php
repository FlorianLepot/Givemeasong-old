<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatsSong
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\StatsSongRepository")
 */
class StatsSong
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
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var integer
     *
     * @ORM\Column(name="favorites", type="integer")
     */
    private $favorites;

    /**
     * @var integer
     *
     * @ORM\Column(name="nexts", type="integer")
     */
    private $nexts;

    /**
     * @var integer
     *
     * @ORM\Column(name="hates", type="integer")
     */
    private $hates;

    public function __construct() {
        $this->views = 0;
        $this->favorites = 0;
        $this->nexts = 0;
        $this->hates = 0;
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
     * Set views
     *
     * @param integer $views
     * @return StatsSong
     */
    public function setViews($views)
    {
        $this->views += $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set favorites
     *
     * @param integer $favorites
     * @return StatsSong
     */
    public function setFavorites($favorites)
    {
        $this->favorites += $favorites;

        return $this;
    }

    /**
     * Get favorites
     *
     * @return integer
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * Set nexts
     *
     * @param integer $nexts
     * @return StatsSong
     */
    public function setNexts($nexts)
    {
        $this->nexts += $nexts;

        return $this;
    }

    /**
     * Get next
     *
     * @return integer
     */
    public function getNexts()
    {
        return $this->nexts;
    }

    /**
     * Set hates
     *
     * @param integer $hates
     * @return StatsSong
     */
    public function setHates($hates)
    {
        $this->hates += $hates;

        return $this;
    }

    /**
     * Get hates
     *
     * @return integer
     */
    public function getHates()
    {
        return $this->hates;
    }
}
