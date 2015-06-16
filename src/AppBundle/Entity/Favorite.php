<?php
// src/AppBundle/Entity/Favorite.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @IgnoreAnnotation("fn")
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="Favorite")
 */
class Favorite
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
     * @ORM\Column(type="string", length=256)
     */
	protected $title;

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
     * Set title
     *
     * @param string $title
     * @return Favorite
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
