<?php

namespace AppBundle\Entity;

use Cake\Chronos\Chronos;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    /**
     * @var
     *
     * @ORM\Column(name="date", type="integer")
     */
    private $date;


    /**
     * @var int
     *
     */
    private $signerId;

    /**
     * @var int
     * 
     * @ORM\Column(name="real_author_id", type="integer")
     */
    private $realAuthorId;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SearchQuery", inversedBy="posts")
     * @ORM\JoinTable(name="posts_search_queries")
     */
    private $searchQueries;


    public function __construct()
    {
        $this->searchQueries = new ArrayCollection();
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
     * @var int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getDate()
    {
        return $this->date;
    }    
    
    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date; 
    }

    /**
     * @return int
     */
    public function getSignerId()
    {
        return $this->signerId;
    }

    /**
     * @param int $signerId
     */
    public function setSignerId($signerId)
    {
        $this->signerId = $signerId;
    }

    /**
     * @return int
     */
    public function getRealAuthorId()
    {
        return $this->realAuthorId;
    }

    /**
     * @param int $realAuthorId
     */
    public function setRealAuthorId($realAuthorId)
    {
        $this->realAuthorId = $realAuthorId;
    }

    /**
     * @return mixed
     */
    public function getSearchQueries()
    {
        return $this->searchQueries;
    }

    public function addSearchQuery(SearchQuery $searchQuery)
    {
        $this->searchQueries->add($searchQuery);
    }

    public function belongsToSearchQuery(SearchQuery $searchQuery)
    {
        return $this->searchQueries->contains($searchQuery);
    }
    
    /**
     * @param mixed $searchQueries
     */
    public function setSearchQueries($searchQueries)
    {
        $this->searchQueries = $searchQueries;
    }

}

