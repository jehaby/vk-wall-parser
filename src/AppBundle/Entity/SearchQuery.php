<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string
     * 
     * @ORM\Column(name="text", type="string", unique=true)
     */
    private $text;
    

    public function __construct($text)
    {
        $this->setText($text);
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

}

