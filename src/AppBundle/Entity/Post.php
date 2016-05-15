<?php

namespace AppBundle\Entity;

use Cake\Chronos\Chronos;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * @var int
     *
     * @ORM\Column(name="signer_id", type="bigint")
     */
    private $signerId;

    /**
     * @var int
     * 
     * @ORM\Column(name="real_author_id", type="integer")
     */
    private $realAuthorId;
    
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

}

