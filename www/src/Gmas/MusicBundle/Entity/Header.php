<?php

namespace Gmas\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Header
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gmas\MusicBundle\Entity\HeaderRepository")
 */
class Header
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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Gmas\MusicBundle\Entity\Categories", inversedBy="songs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;


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
     * Set slug
     *
     * @param string $slug
     * @return Header
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
}
