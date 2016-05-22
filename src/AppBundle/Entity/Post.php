<?php

namespace AppBundle\Entity;

use Cake\Chronos\Chronos;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Exception;

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


    private function __construct()
    {
        $this->searchQueries = new ArrayCollection();
    }


    public static function createFromApiArray(array $data, SearchQuery $query)
    {
        $post = new self();
        $post->id = $data['id'];
        $post->text = $data['text'];
        $post->date = $data['date'];
        $post->realAuthorId = self::determineRealAuthorId($data);
        if ($query) {
            $post->addSearchQuery($query);
        }

        return $post;
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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getSignerId()
    {
        return $this->signerId;
    }

    /**
     * @return int
     */
    public function getRealAuthorId()
    {
        return $this->realAuthorId;
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

    protected static function determineRealAuthorId(array $data)
    {
        if (isset($data['signer_id'])) {
            return $data['signer_id'];
        }
        if (isset($data['copy_history']['owner_id'])) {
            return $data['copy_history']['owner_id'];
        }
        throw new Exception\UnknowAuthorException($data);
    }


}

