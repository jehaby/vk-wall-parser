<?php


namespace AppBundle\Factory;


use AppBundle\Entity\Post;
use AppBundle\Entity\SearchQuery;
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
     * @param array $dataFromApi
     *
     *
     */
    public function createFromArray(array $dataFromApi, SearchQuery $query = null)
    {
        try {
            $post = new Post();
            $post->setId($dataFromApi['id']);
            $post->setDate($dataFromApi['date']);
            $post->setText($dataFromApi['text']);
            $post->setRealAuthorId($this->determineRealAuthorId($dataFromApi));
            if ($query) {
                $post->addSearchQuery($query);
            }
            return $post;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
            throw new PostCreationException($dataFromApi);
        }
    }

    protected function determineRealAuthorId(array $data)
    {
        if (isset($data['signer_id'])) {
            return $data['signer_id'];
        }
        if (isset($data['copy_history']['owner_id'])) {
            return $data['copy_history']['owner_id'];
        }
        throw new UnknowAuthorException($data);
    }


}