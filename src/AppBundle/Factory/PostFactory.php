<?php


namespace AppBundle\Factory;


use AppBundle\Entity\Post;
use Psr\Log\LoggerInterface;

class PostFactory
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param array $data
     *
     *
     */
    public function createFromArray(array $data)
    {
        try {
            $post = new Post();
            $post->setId($data['id']);
            $post->setDate($data['date']);
            $post->setText($data['text']);
            $post->setRealAuthorId($this->determineRealAuthorId($data));
            return $post;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
            throw new PostCreationException($data);
        }
    }


    public function determineRealAuthorId(array $data)
    {
        if (isset($data['signer_id'])) {
            return $data['signer_id'];
        }
        if (isset($data['copy_history']['owner_id'])) {
            isset($data['copy_history']['owner_id']);
        }
        throw new UnknowAuthorException($data);
    }


}