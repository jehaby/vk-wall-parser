<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SearchQuery
 *
 * @ORM\Table(name="search_query")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SearchQueryRepository")
 */
class SearchQuery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SearchQuery", mappedBy="searchQueries")
     */
    private $posts;

    /**
     * @var string
     * 
     * @ORM\Column(name="text", type="string", unique=true)
     */
    private $text;
    

    public function __construct($text)
    {
        $this->setText($text);
        $this->posts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

}

